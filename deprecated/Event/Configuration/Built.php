<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Configuration;

use Auth0\Laravel\Events\Configuration\{BuiltConfigurationEventAbstract, BuiltConfigurationEventContract};

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\Configuration\BuiltConfigurationEvent instead
 *
 * @api
 */
final class Built extends BuiltConfigurationEventAbstract implements BuiltConfigurationEventContract
{
}
