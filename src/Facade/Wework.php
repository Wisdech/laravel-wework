<?php

namespace XuDev\Wework\Facade;

use Illuminate\Support\Facades\Facade;
use XuDev\Wework\WeworkSDK;

/**
 * @method static string buildLoginUri(string $redirectUrl, string $state, bool $inApp = false)
 * @method static array getUserInfo(string $code)
 */
class Wework extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return WeworkSDK::class;
    }
}
