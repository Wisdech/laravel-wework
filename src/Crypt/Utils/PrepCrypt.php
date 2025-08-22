<?php

namespace XuDev\Wework\Crypt\Utils;

use Exception;
use XuDev\Wework\Exception\CryptException;

class PrepCrypt
{
    public $key = null;

    public $iv = null;

    /**
     * Prpcrypt constructor.
     */
    public function __construct($k)
    {
        $this->key = base64_decode($k.'=');
        $this->iv = substr($this->key, 0, 16);

    }

    /**
     * 加密
     *
     * @return string
     */
    public function encrypt($text, $receiveId)
    {
        try {

            $text = $this->getRandomStr().pack('N', strlen($text)).$text.$receiveId;

            $pkc_encoder = new PKCS7;
            $text = $pkc_encoder->encode($text);

            $encrypted = openssl_encrypt(
                $text,
                'AES-256-CBC',
                $this->key,
                OPENSSL_ZERO_PADDING,
                $this->iv
            );

            return $encrypted;
        } catch (Exception) {
            throw new CryptException(-40006);
        }
    }

    /**
     * 解密
     *
     * @throws CryptException
     */
    public function decrypt($encrypted, $receiveId): ?string
    {
        try {
            $decrypted = openssl_decrypt(
                $encrypted,
                'AES-256-CBC',
                $this->key,
                OPENSSL_ZERO_PADDING,
                $this->iv
            );
        } catch (Exception) {
            throw new CryptException(-40007);
        }
        try {
            // 删除PKCS#7填充
            $pkc_encoder = new PKCS7;
            $result = $pkc_encoder->decode($decrypted);
            if (strlen($result) < 16) {
                return null;
            }
            // 拆分
            $content = substr($result, 16, strlen($result));
            $len_list = unpack('N', substr($content, 0, 4));
            $xml_len = $len_list[1];
            $xml_content = substr($content, 4, $xml_len);
            $from_receiveId = substr($content, $xml_len + 4);
        } catch (Exception) {
            throw new CryptException(-40008);
        }
        if ($from_receiveId != $receiveId) {
            throw new CryptException(-40005);
        }

        return $xml_content;
    }

    /**
     * 生成随机字符串
     */
    private function getRandomStr(): string
    {
        $str = '';
        $str_pol = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyl';
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }

        return $str;
    }
}
