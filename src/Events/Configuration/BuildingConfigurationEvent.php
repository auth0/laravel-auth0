<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events\Configuration;

/**
 * Dispatched immediately before the Auth0 SDK configuration object is built, allowing for modification of the configuration array.
 *
 * @api
 */
final class BuildingConfigurationEvent extends BuildingConfigurationEventAbstract implements BuildingConfigurationEventContract
{
}
