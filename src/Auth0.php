<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Auth0\Laravel\Cache\LaravelCachePool;
use Auth0\Laravel\Contract\Auth0 as ServiceContract;
use Auth0\Laravel\Event\Configuration\{Building, Built};
use Auth0\Laravel\Store\LaravelSession;
use Auth0\SDK\Auth0 as SDK;
use Auth0\SDK\Configuration\SdkConfiguration as Configuration;
use Auth0\SDK\Contract\Auth0Interface as SDKContract;
use Auth0\SDK\Utility\HttpTelemetry;

use function in_array;

/**
 * Service that provides access to the Auth0 SDK.
 */
final class Auth0 implements ServiceContract
{
    /**
     * The Laravel-Auth0 SDK version:.
     *
     * @var string
     */
    public const VERSION = '7.5.1';

    public function __construct(
        private ?SDKContract $sdk = null,
        private ?Configuration $configuration = null,
    ) {
    }

    /**
     * Updates the Auth0 PHP SDK's telemetry to include the correct Laravel markers.
     */
    private function setSdkTelemetry(): self
    {
        HttpTelemetry::setEnvProperty('Laravel', app()->version());
        HttpTelemetry::setPackage('laravel-auth0', self::VERSION);

        return $this;
    }

    public function getConfiguration(): Configuration
    {
        if (null === $this->configuration) {
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

            $event = new Building($config);
            event($event);

            $configuration = new Configuration($event->getConfiguration());

            if (! in_array($configuration->getStrategy(), [Configuration::STRATEGY_API, Configuration::STRATEGY_MANAGEMENT_API], true)) {
                // If no sessionStorage is defined, use an LaravelSession store instance.
                if (! isset($config['sessionStorage'])) {
                    $sessionStore = app(LaravelSession::class, [
                        'prefix' => $configuration->getSessionStorageId(),
                    ]);

                    $configuration->setSessionStorage(sessionStorage: $sessionStore);
                }

                // If no transientStorage is defined, use an LaravelSession store instance.
                if (! isset($config['transientStorage'])) {
                    $transientStore = app(LaravelSession::class, [
                        'prefix' => $configuration->getTransientStorageId(),
                    ]);

                    $configuration->setTransientStorage(transientStorage: $transientStore);
                }
            }

            $this->configuration = $configuration;

            // Give apps an opportunity to mutate the configuration before applying it.
            $event = new Built($configuration);
            event($event);

            $this->configuration = $event->getConfiguration();
        }

        return $this->configuration;
    }

    public function getCredentials(): ?object
    {
        return $this->getSdk()->getCredentials();
    }

    public function getSdk(): SDKContract
    {
        if (null !== $this->sdk) {
            return $this->sdk;
        }

        return $this->setSdk(new SDK($this->getConfiguration()));
    }

    public function reset(): self
    {
        unset($this->sdk, $this->configuration);

        $this->sdk           = null;
        $this->configuration = null;

        return $this;
    }

    public function setConfiguration(Configuration $configuration): self
    {
        $this->configuration = $configuration;

        if (null !== $this->sdk) {
            $this->sdk->setConfiguration($configuration);
        }

        return $this;
    }

    public function setSdk(SDKContract $sdk): SDKContract
    {
        $this->configuration = $sdk->configuration();
        $this->sdk           = $sdk;

        $this->setSdkTelemetry();

        return $this->sdk;
    }
}
