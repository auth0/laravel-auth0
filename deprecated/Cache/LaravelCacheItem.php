<?php

declare(strict_types=1);

namespace Auth0\Laravel\Cache;

use Auth0\Laravel\Bridges\CacheItemBridgeAbstract;

/**
 * Provides a bridge between Laravel's cache API and PSR-6/PSR-16.
 *
 * @codeCoverageIgnore
 * @deprecated 7.8.0 Use Auth0\Laravel\Bridges\CacheItemBridge instead.
 * @internal
 * @api
 */
final class LaravelCacheItem extends CacheItemBridgeAbstract
{
}
