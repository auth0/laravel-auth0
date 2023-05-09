<?php

declare(strict_types=1);

namespace Auth0\Laravel\Entities;

use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Contract\API\ManagementInterface;
use Auth0\SDK\Contract\Auth0Interface as Sdk;

interface ConfigurationContract
{
    /**
     * Create/return instance of the Auth0-PHP SdkConfiguration.
     */
    public function getConfiguration(): SdkConfiguration;

    /**
     * Create/return instance of the Auth0-PHP SDK.
     */
    public function getSdk(): Sdk;

    /**
     * Returns an instance of the Management API class.
     */
    public function management(): ManagementInterface;

    /**
     * Resets and cleans up the internal state of the SDK.
     */
    public function reset(): self;

    /**
     * Assign the Auth0-PHP SdkConfiguration.
     *
     * @param SdkConfiguration $configuration
     */
    public function setConfiguration(SdkConfiguration $configuration): self;

    /**
     * Create/return instance of the Auth0-PHP SDK.
     *
     * @param Sdk $sdk
     */
    public function setSdk(Sdk $sdk): Sdk;
}
