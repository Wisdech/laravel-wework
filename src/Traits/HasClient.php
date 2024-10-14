<?php

namespace Wisdech\Wework\Traits;

use GuzzleHttp\Client;

trait HasClient
{
    /**
     * HTTP 客户端
     * @var Client|null
     */
    protected ?Client $client;

    /**
     * 初始化 HTTP 客户端
     * @return void
     */
    protected function setHttpClient(): void
    {
        $this->client = new Client();
    }
}
