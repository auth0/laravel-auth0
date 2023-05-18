<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Configuration;

use Auth0\Laravel\Events\Configuration\BuildingConfigurationEvent;

/**
 * Event fired when the configuration array is being built.
 *
 * @codeCoverageIgnore
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\Configuration\BuildingConfigurationEvent instead.
 * @api
 */
final class Building extends BuildingConfigurationEvent
{
}
