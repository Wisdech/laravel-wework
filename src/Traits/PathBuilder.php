<?php

namespace Wisdech\Wework\Traits;

trait PathBuilder
{
    /**
     * 获取完整请求 uri
     * @param string $uri
     * @return string
     */
    protected function buildUri(string $uri): string
    {
        return $this->host . $uri;
    }
}
