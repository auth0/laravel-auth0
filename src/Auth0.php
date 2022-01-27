<?php

declare(strict_types=1);

namespace Auth0\Laravel;

/**
 * Service that provides access to the Auth0 SDK.
 */
final class Auth0 implements \Auth0\Laravel\Contract\Auth0
{
    /**
     * The Laravel-Auth0 SDK version:
     */
    public const VERSION = '7.0.0';

    /**
     * An instance of the Auth0-PHP SDK.
     */
    private ?\Auth0\SDK\Auth0 $sdk = null;

    /**
     * An instance of the Auth0-PHP SDK's SdkConfiguration, which handles configuration state.
     */
    private ?\Auth0\SDK\Configuration\SdkConfiguration $configuration = null;

    /**
     * @inheritdoc
     */
    public function getSdk(): \Auth0\SDK\Auth0
    {
        if ($this->sdk === null) {
            $this->sdk = new \Auth0\SDK\Auth0($this->getConfiguration());
            $this->setSdkTelemetry();
        }

        return $this->sdk;
    }

    /**
     * @inheritdoc
     */
    public function setSdk(
        \Auth0\SDK\Auth0 $sdk
    ): self {
        $this->sdk = $sdk;
        $this->setSdkTelemetry();
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getConfiguration(): \Auth0\SDK\Configuration\SdkConfiguration
    {
        if ($this->configuration === null) {
            $this->configuration = new \Auth0\SDK\Configuration\SdkConfiguration(app()->make('config')->get('auth0'));
        }

        return $this->configuration;
    }

    /**
     * @inheritdoc
     */
    public function setConfiguration(
        \Auth0\SDK\Configuration\SdkConfiguration $configuration
    ): self {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getState(): \Auth0\Laravel\StateInstance
    {
        return app()->make(\Auth0\Laravel\StateInstance::class);
    }

    /**
     * Updates the Auth0 PHP SDK's telemetry to include the correct Laravel markers.
     */
    private function setSdkTelemetry(): self
    {
        \Auth0\SDK\Utility\HttpTelemetry::setEnvProperty('Laravel', app()->version());
        \Auth0\SDK\Utility\HttpTelemetry::setPackage('laravel-auth0', self::VERSION);

        return $this;
    }
}
