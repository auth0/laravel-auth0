<?php

declare(strict_types=1);

namespace Auth0\Laravel\Bridges;

/**
 * Bridges the Laravel's Cache API with the PSR-6's CacheItemInterface interface.
 *
 * @internal
 *
 * @api
 */
final class CacheItemBridge extends CacheItemBridgeAbstract
{
    /**
     * Return a LaravelCacheItem instance flagged as missed.
     *
     * @param string $key
     */
    public static function miss(string $key): self
    {
        return new self(
            key: $key,
            value: null,
            hit: false,
        );
    }
}
