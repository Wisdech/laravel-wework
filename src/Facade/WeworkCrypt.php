<?php

namespace XuDev\Wework\Facade;

use Illuminate\Support\Facades\Facade;
use XuDev\Wework\WeworkCryptApi as CryptInstance;

/**
 * @method static string buildLoginUri(string $redirectUrl, string $state, bool $inApp = false)
 * @method static array getUserInfo(string $code)
 */
class WeworkCrypt extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CryptInstance::class;
    }
}
