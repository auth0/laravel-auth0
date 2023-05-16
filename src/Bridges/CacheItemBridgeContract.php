<?php

declare(strict_types=1);

namespace Auth0\Laravel\Bridges;

use Psr\Cache\CacheItemInterface;

/**
 * @api
 */
interface CacheItemBridgeContract extends BridgeContract, CacheItemInterface
{
    /**
     * Return a LaravelCacheItem instance flagged as missed.
     *
     * @param string $key
     */
    public static function miss(string $key): self;
}
