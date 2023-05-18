<?php

declare(strict_types=1);

namespace Auth0\Laravel\Store;

use Auth0\Laravel\Bridges\{SessionBridgeAbstract, SessionBridgeContract};

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Bridges\SessionBridge instead.
 *
 * @internal
 *
 * @api
 */
final class LaravelSession extends SessionBridgeAbstract implements SessionBridgeContract
{
}
