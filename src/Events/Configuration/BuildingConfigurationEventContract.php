<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events\Configuration;

use Auth0\Laravel\Events\EventContract;

/**
 * @api
 */
interface BuildingConfigurationEventContract extends EventContract
{
    /**
     * AuthenticationFailed constructor.
     *
     * @param array $configuration a configuration array for use with the Auth0-PHP SDK
     */
    public function __construct(array $configuration);

    /**
     * Returns the exception to be thrown.
     */
    public function getConfiguration(): array;

    /**
     * Determine whether the provided exception will be thrown by the SDK.
     *
     * @param array $configuration an configuration array for use with the Auth0-PHP SDK
     */
    public function setConfiguration(array $configuration): void;
}
