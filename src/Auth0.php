<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Auth0\Laravel\Cache\LaravelCachePool;
use Auth0\Laravel\Store\LaravelSession;

/**
 * Service that provides access to the Auth0 SDK.
 */
final class Auth0 implements \Auth0\Laravel\Contract\Auth0
{
    /**
     * The Laravel-Auth0 SDK version:
     */
    public const VERSION = '7.1.0';

    /**
     * An instance of the Auth0-PHP SDK.
     */
    private ?\Auth0\SDK\Contract\Auth0Interface $sdk = null;

    /**
     * An instance of the Auth0-PHP SDK's SdkConfiguration, which handles configuration state.
     */
    private ?\Auth0\SDK\Configuration\SdkConfiguration $configuration = null;

    /**
     * @inheritdoc
     */
    public function getSdk(): \Auth0\SDK\Contract\Auth0Interface
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
    public function setSdk(\Auth0\SDK\Contract\Auth0Interface $sdk): self
    {
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
            $config = app()
                ->make('config')
                ->get('auth0');

            if (! isset($config['tokenCache']) || ! isset($config['managementTokenCache'])) {
                $cache = new LaravelCachePool();

                if (! isset($config['tokenCache'])) {
                    $config['tokenCache'] = $cache;
                }

                if (! isset($config['managementTokenCache'])) {
                    $config['managementTokenCache'] = $cache;
                }
            }

            $configuration = new \Auth0\SDK\Configuration\SdkConfiguration($config);

            // If no sessionStorage is defined, use an LaravelSession store instance.
            if (! isset($config['sessionStorage'])) {
                $configuration->setSessionStorage(
                    new LaravelSession($configuration, $configuration->getSessionStorageId())
                );
            }

            // If no transientStorage is defined, use an LaravelSession store instance.
            if (! isset($config['transientStorage'])) {
                $configuration->setTransientStorage(
                    new LaravelSession($configuration, $configuration->getSessionStorageId())
                );
            }

            // Give apps an opportunity to mutate the configuration before applying it.
            $event = new \Auth0\Laravel\Event\Configuration\Built($configuration);
            event($event);

            $this->configuration = $event->getConfiguration();
        }

        return $this->configuration;
    }

    /**
     * @inheritdoc
     */
    public function setConfiguration(\Auth0\SDK\Configuration\SdkConfiguration $configuration): self
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getState(): \Auth0\Laravel\Contract\StateInstance
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
