<?php

namespace XuDev\Wework\Exception;

use Exception;

class CryptException extends Exception
{
    public function __construct(int $code = 0, ?string $message = null)
    {
        if (! $message) {
            $message = match ($code) {
                -40001 => '签名验证错误',
                -40002 => 'XML解析失败',
                -40004 => 'EncodingAesKey非法',
                -40005 => 'CorpId校验错误',
                -40008 => '解密后得到的Buffer非法',
                -40009 => 'Base64加密失败',
                -40010 => 'Base64解密失败',
                -40011 => '生成XML失败',
            };
        }

        parent::__construct("企业微信加解密错误：[$code] $message", $code);
    }
}
