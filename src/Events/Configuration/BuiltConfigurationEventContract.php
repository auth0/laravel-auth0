<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events\Configuration;

use Auth0\Laravel\Events\EventContract;
use Auth0\SDK\Configuration\SdkConfiguration;

/**
 * @api
 */
interface BuiltConfigurationEventContract extends EventContract
{
    /**
     * AuthenticationFailed constructor.
     *
     * @param SdkConfiguration $configuration an instance of SdkConfiguration for use with the Auth0-PHP SDK
     */
    public function __construct(SdkConfiguration $configuration);

    /**
     * Returns the exception to be thrown.
     */
    public function getConfiguration(): SdkConfiguration;

    /**
     * Determine whether the provided exception will be thrown by the SDK.
     *
     * @param SdkConfiguration $configuration an instance of SdkConfiguration for use with the Auth0-PHP SDK
     */
    public function setConfiguration(SdkConfiguration $configuration): void;
}
