<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract;

use Auth0\SDK\Configuration\SdkConfiguration as Configuration;
use Auth0\SDK\Contract\API\ManagementInterface;
use Auth0\SDK\Contract\Auth0Interface as SDK;

interface Auth0
{
    /**
     * Create/return instance of the Auth0-PHP SdkConfiguration.
     */
    public function getConfiguration(): Configuration;

    /**
     * Create/return instance of the Auth0-PHP SDK.
     */
    public function getSdk(): SDK;

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
     * @param Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration): self;

    /**
     * Create/return instance of the Auth0-PHP SDK.
     *
     * @param SDK $sdk
     */
    public function setSdk(SDK $sdk): SDK;
}
