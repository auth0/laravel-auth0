<?php

declare(strict_types=1);

namespace Auth0\Laravel\Store;

use Auth0\Laravel\Bridges\SessionBridgeAbstract;

/**
 * Bridges the Auth0-PHP SDK StoreInterface with the Laravel Session Store API.
 *
 * @codeCoverageIgnore
 * @deprecated 7.8.0 Use Auth0\Laravel\Bridges\SessionBridge instead.
 * @internal
 * @api
 */
final class LaravelSession extends SessionBridgeAbstract
{
}
