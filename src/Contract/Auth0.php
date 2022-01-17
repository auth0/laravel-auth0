<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract;

interface Auth0
{
    /**
     * Create/return instance of the Auth0-PHP SDK.
     */
    public function getSdk(): \Auth0\SDK\Auth0;

    /**
     * Create/return instance of the Auth0-PHP SDK.
     */
    public function setSdk(
        \Auth0\SDK\Auth0 $sdk
    ): self;

    /**
     * Create/return instance of the Auth0-PHP SdkConfiguration.
     */
    public function getConfiguration(): \Auth0\SDK\Configuration\SdkConfiguration;

    /**
     * Assign the Auth0-PHP SdkConfiguration.
     */
    public function setConfiguration(
        \Auth0\SDK\Configuration\SdkConfiguration $configuration
    ): self;

    /**
     * Create/create a request state instance, a storage singleton containing authenticated user data.
     */
    public function getState(): \Auth0\Laravel\StateInstance;
}
