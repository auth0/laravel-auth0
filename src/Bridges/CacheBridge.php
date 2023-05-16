<?php

declare(strict_types=1);

namespace Auth0\Laravel\Bridges;

/**
 * Bridges the Laravel's Cache API with the PSR-6's CacheItemPoolInterface interface.
 *
 * @internal
 *
 * @api
 */
final class CacheBridge extends CacheBridgeAbstract implements CacheBridgeContract
{
}
