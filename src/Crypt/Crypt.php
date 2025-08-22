<?php

namespace XuDev\Wework\Crypt;

use XuDev\Wework\Crypt\Utils\PrepCrypt;
use XuDev\Wework\Crypt\Utils\SHA1;
use XuDev\Wework\Crypt\Utils\XML;
use XuDev\Wework\Exception\CryptException;

class Crypt
{
    private $m_sToken;

    private $m_sEncodingAesKey;

    private $m_sReceiveId;

    /**
     * 构造函数
     *
     * @param  $token  string 开发者设置的token
     * @param  $encodingAesKey  string 开发者设置的EncodingAESKey
     * @param  $receiveId  string, 不同应用场景传不同的id
     */
    public function __construct($token, $encodingAesKey, $receiveId)
    {
        $this->m_sToken = $token;
        $this->m_sEncodingAesKey = $encodingAesKey;
        $this->m_sReceiveId = $receiveId;
    }

    /**
     *验证URL
     *
     *@param sMsgSignature: 签名串，对应URL参数的msg_signature
     *@param sTimeStamp: 时间戳，对应URL参数的timestamp
     *@param sNonce: 随机串，对应URL参数的nonce
     *@param sEchoStr: 随机串，对应URL参数的echostr
     *@param sReplyEchoStr: 解密之后的echostr，当return返回0时有效
     *
     *@return：成功0，失败返回对应的错误码
     */
    public function VerifyURL($sMsgSignature, $sTimeStamp, $sNonce, $sEchoStr, &$sReplyEchoStr)
    {
        if (strlen($this->m_sEncodingAesKey) != 43) {
            return ErrorCode::$IllegalAesKey;
        }

        $pc = new Prpcrypt($this->m_sEncodingAesKey);
        // verify msg_signature
        $sha1 = new SHA1;
        $array = $sha1->getSHA1($this->m_sToken, $sTimeStamp, $sNonce, $sEchoStr);
        $ret = $array[0];

        if ($ret != 0) {
            return $ret;
        }

        $signature = $array[1];
        if ($signature != $sMsgSignature) {
            return ErrorCode::$ValidateSignatureError;
        }

        $result = $pc->decrypt($sEchoStr, $this->m_sReceiveId);
        if ($result[0] != 0) {
            return $result[0];
        }
        $sReplyEchoStr = $result[1];

        return ErrorCode::$OK;
    }

    /**
     * 将公众平台回复用户的消息加密打包.
     * <ol>
     *    <li>对要发送的消息进行AES-CBC加密</li>
     *    <li>生成安全签名</li>
     *    <li>将消息密文和安全签名打包成json格式</li>
     * </ol>
     *
     * @param  $replyMsg  string 公众平台待回复用户的消息，json格式的字符串
     * @param  $timeStamp  string 时间戳，可以自己生成，也可以用URL参数的timestamp
     * @param  $nonce  string 随机串，可以自己生成，也可以用URL参数的nonce
     * @param  &$encryptMsg  string 加密后的可以直接回复用户的密文，包括msg_signature, timestamp, nonce, encrypt的json格式的字符串,
     *                      当return返回0时有效
     * @return int 成功0，失败返回对应的错误码
     */
    public function EncryptMsg($sReplyMsg, $sTimeStamp, $sNonce, &$sEncryptMsg)
    {
        $pc = new Prpcrypt($this->m_sEncodingAesKey);

        // 加密
        $array = $pc->encrypt($sReplyMsg, $this->m_sReceiveId);
        $ret = $array[0];
        if ($ret != 0) {
            return $ret;
        }

        if ($sTimeStamp == null) {
            $sTimeStamp = time();
        }
        $encrypt = $array[1];

        // 生成安全签名
        $sha1 = new SHA1;
        $array = $sha1->getSHA1($this->m_sToken, $sTimeStamp, $sNonce, $encrypt);
        $ret = $array[0];
        if ($ret != 0) {
            return $ret;
        }
        $signature = $array[1];

        // 生成发送的json
        $jsonParse = new JsonParse;
        $sEncryptMsg = $jsonParse->generate($encrypt, $signature, $sTimeStamp, $sNonce);

        return ErrorCode::$OK;
    }

    /**
     * 检验消息的真实性，并且获取解密后的明文.
     *
     * @param  $signature  string 签名串，对应URL参数的msg_signature
     * @param  $nonce  string 随机串，对应URL参数的nonce
     * @param  $postData  string 密文，对应POST请求的数据
     * @param  $timestamp  string 时间戳 对应URL参数的timestamp
     * @return string 获取解密后的明文
     *
     * @throws CryptException
     */
    public function decryptMessage(string $signature, string $nonce, string $postData, string $timestamp): string
    {
        if (strlen($this->m_sEncodingAesKey) != 43) {
            throw new CryptException(-40004);
        }

        $pc = new PrepCrypt($this->m_sEncodingAesKey);

        $encrypt = XML::extract($postData);

        $localSignature = SHA1::getSHA1($this->m_sToken, $timestamp, $nonce, $encrypt);

        if ($localSignature != $signature) {
            throw new CryptException(-40001);
        }

        return $pc->decrypt($encrypt, $this->m_sReceiveId);
    }
}
