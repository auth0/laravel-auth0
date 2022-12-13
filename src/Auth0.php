<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Auth0\Laravel\Cache\LaravelCachePool;
use Auth0\Laravel\Store\LaravelSession;
use Auth0\SDK\Configuration\SdkConfiguration as Configuration;
use Auth0\SDK\Contract\Auth0Interface as SDK;

/**
 * Service that provides access to the Auth0 SDK.
 */
final class Auth0 implements \Auth0\Laravel\Contract\Auth0
{
    /**
     * The Laravel-Auth0 SDK version:.
     */
    public const VERSION = '7.4.0';

    /**
     * An instance of the Auth0-PHP SDK.
     */
    private static ?SDK $sdk = null;

    /**
     * An instance of the Auth0-PHP SDK's SdkConfiguration, which handles configuration state.
     */
    private static ?Configuration $configuration = null;

    /**
     * {@inheritdoc}
     */
    public function getSdk(): SDK
    {
        if (null === self::$sdk) {
            self::$sdk = new \Auth0\SDK\Auth0($this->getConfiguration());
        }

        $this->setSdkTelemetry();

        return self::$sdk;
    }

    /**
     * {@inheritdoc}
     */
    public function setSdk(SDK $sdk): self
    {
        self::$sdk = $sdk;
        $this->setSdkTelemetry();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): Configuration
    {
        if (null === self::$configuration) {
            $config = config('auth0');

            /**
             * @var array<mixed> $config
             */
            if (! isset($config['tokenCache']) || ! isset($config['managementTokenCache'])) {
                $cache = new LaravelCachePool();

                if (! isset($config['tokenCache'])) {
                    $config['tokenCache'] = $cache;
                }

                if (! isset($config['managementTokenCache'])) {
                    $config['managementTokenCache'] = $cache;
                }
            }

            $event = new \Auth0\Laravel\Event\Configuration\Building($config);
            event($event);

            $configuration = new Configuration($event->getConfiguration());

            if (! \in_array($configuration->getStrategy(), [Configuration::STRATEGY_API, Configuration::STRATEGY_MANAGEMENT_API], true)) {
                // If no sessionStorage is defined, use an LaravelSession store instance.
                if (! isset($config['sessionStorage'])) {
                    $configuration->setSessionStorage(
                        sessionStorage: new LaravelSession(
                            prefix: $configuration->getSessionStorageId(),
                        ),
                    );
                }

                // If no transientStorage is defined, use an LaravelSession store instance.
                if (! isset($config['transientStorage'])) {
                    $configuration->setTransientStorage(
                        transientStorage: new LaravelSession(
                            prefix: $configuration->getTransientStorageId(),
                        ),
                    );
                }
            }

            // Give apps an opportunity to mutate the configuration before applying it.
            $event = new \Auth0\Laravel\Event\Configuration\Built($configuration);
            event($event);

            self::$configuration = $event->getConfiguration();
        }

        return self::$configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(Configuration $configuration): self
    {
        self::$configuration = $configuration;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getState(): Contract\StateInstance
    {
        return app(StateInstance::class);
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
