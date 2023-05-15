<?php

declare(strict_types=1);

namespace Auth0\Laravel\Bridges;

use Psr\Cache\CacheItemInterface;

/**
 * @api
 */
interface CacheItemBridgeContract extends BridgeContract, CacheItemInterface
{
}
