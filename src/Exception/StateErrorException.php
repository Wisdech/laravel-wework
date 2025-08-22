<?php

namespace XuDev\Wework\Exception;

use Exception;

class StateErrorException extends Exception
{
    public function __construct()
    {
        parent::__construct('认证服务器返回的State与系统内不一致');
    }
}
