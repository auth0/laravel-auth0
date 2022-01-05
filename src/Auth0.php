<?php

declare(strict_types=1);

namespace Auth0\Laravel;

/**
 * Service that provides access to the Auth0 SDK.
 */
final class Auth0
{
    /**
     * The Laravel-Auth0 SDK version:
     */
    public const SDK_VERSION = '7.0.0';

    /**
     * An instance of the Auth0-PHP SDK.
     */
    private ?\Auth0\SDK\Auth0 $sdk = null;

    /**
     * An instance of the Auth0-PHP SDK's SdkConfiguration, which handles configuration state.
     */
    private ?\Auth0\SDK\Configuration\SdkConfiguration $configuration = null;

    /**
     * Create/return instance of the Auth0-PHP SDK.
     */
    public function getSdk(): \Auth0\SDK\Auth0
    {
        if ($this->sdk === null) {
            $this->sdk = new \Auth0\SDK\Auth0($this->getConfiguration());
        }

        return $this->sdk;
    }

    /**
     * Create/return instance of the Auth0-PHP SdkConfiguration.
     */
    public function getConfiguration(): \Auth0\SDK\Configuration\SdkConfiguration
    {
        if ($this->configuration === null) {
            $this->configuration = new \Auth0\SDK\Configuration\SdkConfiguration(app()->make('config')->get('auth0'));
        }

        return $this->configuration;
    }

    /**
     * Create/create a request state instance, a storage singleton containing authenticated user data.
     */
    public function getState(): \Auth0\Laravel\StateInstance
    {
        return app()->make(\Auth0\Laravel\StateInstance::class);
    }
}
