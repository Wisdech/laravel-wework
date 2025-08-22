<?php

namespace XuDev\Wework\Exception;

use Exception;

class CallbackException extends Exception
{
    public function __construct()
    {
        parent::__construct('认证服务器返回数据有误');
    }
}
