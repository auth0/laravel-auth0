<?php

declare(strict_types=1);

namespace Auth0\Laravel\Cache;

use Auth0\Laravel\Bridges\{CacheBridgeAbstract, CacheBridgeContract};

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Bridges\CacheBridge instead.
 *
 * @internal
 *
 * @api
 */
final class LaravelCachePool extends CacheBridgeAbstract implements CacheBridgeContract
{
}
