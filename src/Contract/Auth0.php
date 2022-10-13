<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract;

use Auth0\SDK\Configuration\SdkConfiguration as Configuration;
use Auth0\SDK\Contract\Auth0Interface as SDK;

interface Auth0
{
    /**
     * Create/return instance of the Auth0-PHP SDK.
     */
    public function getSdk(): SDK;

    /**
     * Create/return instance of the Auth0-PHP SDK.
     */
    public function setSdk(SDK $sdk): self;

    /**
     * Create/return instance of the Auth0-PHP SdkConfiguration.
     */
    public function getConfiguration(): Configuration;

    /**
     * Assign the Auth0-PHP SdkConfiguration.
     */
    public function setConfiguration(Configuration $configuration): self;

    /**
     * Create/create a request state instance, a storage singleton containing authenticated user data.
     */
    public function getState(): StateInstance;
}
