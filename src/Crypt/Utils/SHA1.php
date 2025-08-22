<?php

namespace XuDev\Wework\Crypt\Utils;

use Exception;
use XuDev\Wework\Exception\CryptException;

class SHA1
{
    /**
     * 用SHA1算法生成安全签名
     *
     * @param  string  $token  票据
     * @param  string  $timestamp  时间戳
     * @param  string  $nonce  随机字符串
     * @param  string  $encrypt  密文消息
     *
     * @throws CryptException
     */
    public static function getSHA1(string $token, string $timestamp, string $nonce, string $encrypt): string
    {
        try {
            $array = [$encrypt, $token, $timestamp, $nonce];
            sort($array, SORT_STRING);
            $str = implode($array);

            return sha1($str);
        } catch (Exception) {
            throw new CryptException(-40003);
        }
    }
}
