<?php

namespace Wisdech\Wework\Traits;

use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;

trait HasCache
{
    /**
     * 缓存对象
     * @var CacheItem|null
     */
    protected ?CacheItem $cache;

    /**
     * 初始化缓存对象
     * @throws InvalidArgumentException
     */
    protected function setCacheItem(): void
    {
        $this->cache = (new FilesystemAdapter())->getItem($this->cacheKey);
    }
}
