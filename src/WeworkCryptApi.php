<?php

namespace XuDev\Wework;

use XuDev\Wework\Exception\CryptException;
use XuDev\Wework\WeworkCryptApi\CryptoCore;
use XuDev\Wework\WeworkCryptApi\XMLParser;

class WeworkCryptApi
{
    private string $token;

    private string $encodingAesKey;

    private string $corpId;

    public function __construct()
    {
        $this->token = config('wework.message_token');
        $this->encodingAesKey = config('wework.encoding_aes_key');
        $this->corpId = config('wework.corp_id');

        if ($this->encodingAesKey && strlen($this->encodingAesKey) != 43) {
            throw new CryptException(-40004);
        }
    }

    private function signature(string $token, string $timestamp, string $nonce, string $encrypt): string
    {
        $array = [$encrypt, $token, $timestamp, $nonce];
        sort($array, SORT_STRING);
        $string = implode($array);

        return sha1($string);
    }

    private function verifySignature($signature, $timestamp, $nonce, $encrypt): void
    {
        $localSignature = $this->signature($this->token, $timestamp, $nonce, $encrypt);

        if ($localSignature != $signature) {
            throw new CryptException(-40001);
        }
    }

    /**
     * 验证URL
     * @throws CryptException
     */
    public function verifyURL($signature, $timestamp, $nonce, $encrypt): ?string
    {
        $this->verifySignature($signature, $timestamp, $nonce, $encrypt);

        return (new CryptoCore)->decrypt($encrypt, $this->corpId);
    }

    /**
     * 加密消息
     * @param string $message
     * @param string $nonce
     * @param string|null $timestamp
     * @return string
     */
    public function encryptMessage(string $message, string $nonce, ?string $timestamp = null): string
    {
        $timestamp = $timestamp ?: time();

        $encrypt = (new CryptoCore)->encrypt($message, $this->corpId);

        $signature = $this->signature($this->token, $timestamp, $nonce, $encrypt);

        return XMLParser::generate($encrypt, $signature, $timestamp, $nonce);
    }

    /**
     * 解密XML消息
     * @param string $signature
     * @param string $nonce
     * @param string $postData
     * @param string|null $timestamp
     * @return string
     * @throws CryptException
     */
    public function decryptMessage(string $signature, string $nonce, string $postData, ?string $timestamp = null): string
    {
        $timestamp = $timestamp ?: time();

        $encrypt = XMLParser::extract($postData);

        $this->verifySignature($signature, $timestamp, $nonce, $encrypt);

        return (new CryptoCore)->decrypt($encrypt, $this->corpId);
    }
}
