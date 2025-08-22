<?php

namespace XuDev\Wework\Utils;

use Illuminate\Support\Str;
use XuDev\Wework\Exception\CryptException;

class CryptoCore
{
    public string $key;

    public string $iv;

    public function __construct()
    {
        $key = config('wework.encoding_aes_key');

        $this->key = base64_decode($key.'=');
        $this->iv = substr($this->key, 0, 16);
    }

    /**
     * 加密
     */
    public function encrypt($text, $receiveId): string
    {
        $text = Str::random().pack('N', strlen($text)).$text.$receiveId;

        $text = PKC7Encoder::encode($text);

        return openssl_encrypt($text, 'AES-256-CBC', $this->key, 2, $this->iv);
    }

    /**
     * 解密
     *
     * @throws CryptException
     */
    public function decrypt($encrypted, $receiveId): ?string
    {
        $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $this->key, 2, $this->iv);

        $result = PKC7Encoder::decode($decrypted);
        if (strlen($result) < 16) {
            return null;
        }
        $content = substr($result, 16, strlen($result));
        $len_list = unpack('N', substr($content, 0, 4));
        $xml_len = $len_list[1];
        $xml_content = substr($content, 4, $xml_len);
        $from_receiveId = substr($content, $xml_len + 4);

        if ($from_receiveId != $receiveId) {
            throw new CryptException(-40005);
        }

        return $xml_content;
    }
}
