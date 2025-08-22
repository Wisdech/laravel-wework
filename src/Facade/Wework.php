<?php

namespace XuDev\Wework\Facade;

use Illuminate\Support\Facades\Facade;
use XuDev\Wework\WeworkApi as WeworkInstance;

class Wework extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return WeworkInstance::class;
    }
}
