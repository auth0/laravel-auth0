<?php

declare(strict_types=1);

namespace Auth0\Laravel\Bridges;

use Psr\Cache\CacheItemPoolInterface;

/**
 * @api
 */
interface CacheBridgeContract extends BridgeContract, CacheItemPoolInterface
{
}
