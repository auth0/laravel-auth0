<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events\Configuration;

/**
 * Event fired when the configuration array is being built.
 *
 * @api
 */
final class BuildingConfigurationEvent extends BuildingConfigurationEventAbstract implements BuildingConfigurationEventContract
{
}
