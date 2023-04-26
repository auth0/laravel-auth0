<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Auth0\Laravel\Cache\LaravelCachePool;
use Auth0\Laravel\Contract\Auth0 as ServiceContract;
use Auth0\Laravel\Event\Configuration\{Building, Built};
use Auth0\Laravel\Store\LaravelSession;
use Auth0\SDK\Auth0 as SDK;
use Auth0\SDK\Configuration\SdkConfiguration as Configuration;
use Auth0\SDK\Contract\API\ManagementInterface;
use Auth0\SDK\Contract\{Auth0Interface as SDKContract, StoreInterface};
use Auth0\SDK\Utility\HttpTelemetry;
use Psr\Cache\CacheItemPoolInterface;

use function in_array;
use function is_string;

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
    public const VERSION = '7.6.0';

    public function __construct(
        private ?SDKContract $sdk = null,
        private ?Configuration $configuration = null,
        private ?CacheItemPoolInterface $tokenCachePool = null,
        private ?CacheItemPoolInterface $managementTokenCachePool = null,
    ) {
    }

    private function bootManagementTokenCache(array $config): array
    {
        $managementTokenCache = $config['managementTokenCache'] ?? null;

        if (null === $managementTokenCache) {
            $config['managementTokenCache'] = $this->getManagementTokenCachePool();
        }

        if (false === $managementTokenCache) {
            $config['managementTokenCache'] = null;
        }

        if (is_string($managementTokenCache)) {
            $config['managementTokenCache'] = null;

            $managementTokenCache = app(trim($managementTokenCache));

            if ($managementTokenCache instanceof CacheItemPoolInterface) {
                $config['managementTokenCache'] = $managementTokenCache;
            }
        }

        return $config;
    }

    private function bootSessionStorage(array $config): array
    {
        $sessionStorage   = $config['sessionStorage'] ?? null;
        $sessionStorageId = $config['sessionStorageId'] ?? 'auth0_session';

        if (null === $sessionStorage) {
            $config['sessionStorage'] = app(LaravelSession::class, [
                'prefix' => $sessionStorageId,
            ]);
        }

        if (false === $sessionStorage) {
            $config['sessionStorage'] = null;
        }

        if (is_string($sessionStorage)) {
            $config['sessionStorage'] = null;

            $sessionStorage = app(trim($sessionStorage), [
                'prefix' => $sessionStorageId,
            ]);

            if ($sessionStorage instanceof StoreInterface) {
                $config['sessionStorage'] = $sessionStorage;
            }
        }

        return $config;
    }

    private function bootStrategy(array $config): array
    {
        $strategy = $config['strategy'] ?? Configuration::STRATEGY_REGULAR;

        if (! is_string($strategy)) {
            $strategy = Configuration::STRATEGY_REGULAR;
        }

        $config['strategy'] = $strategy;

        return $config;
    }

    private function bootTokenCache(array $config): array
    {
        $tokenCache = $config['tokenCache'] ?? null;

        if (null === $tokenCache) {
            $config['tokenCache'] = $this->getTokenCachePool();
        }

        if (false === $tokenCache) {
            $config['tokenCache'] = null;
        }

        if (is_string($tokenCache)) {
            $config['tokenCache'] = null;

            $tokenCache = app(trim($tokenCache));

            if ($tokenCache instanceof CacheItemPoolInterface) {
                $config['tokenCache'] = $tokenCache;
            }
        }

        return $config;
    }

    private function bootTransientStorage(array $config): array
    {
        $transientStorage   = $config['transientStorage'] ?? null;
        $transientStorageId = $config['transientStorageId'] ?? 'auth0_transient';

        if (null === $transientStorage) {
            $config['transientStorage'] = app(LaravelSession::class, [
                'prefix' => $transientStorageId,
            ]);
        }

        if (false === $transientStorage) {
            $config['transientStorage'] = null;
        }

        if (is_string($transientStorage)) {
            $config['transientStorage'] = null;

            $transientStorage = app(trim($transientStorage), [
                'prefix' => $transientStorageId,
            ]);

            if ($transientStorage instanceof StoreInterface) {
                $config['transientStorage'] = $transientStorage;
            }
        }

        return $config;
    }

    private function getManagementTokenCachePool(): CacheItemPoolInterface
    {
        if (! $this->managementTokenCachePool instanceof \Psr\Cache\CacheItemPoolInterface) {
            $this->managementTokenCachePool = app(LaravelCachePool::class);
        }

        return $this->managementTokenCachePool;
    }

    private function getTokenCachePool(): CacheItemPoolInterface
    {
        if (! $this->tokenCachePool instanceof \Psr\Cache\CacheItemPoolInterface) {
            $this->tokenCachePool = app(LaravelCachePool::class);
        }

        return $this->tokenCachePool;
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
        if (! $this->configuration instanceof Configuration) {
            $config = config('auth0');

            /**
             * @var array<mixed> $config
             */

            // Give host application an opportunity to update the configuration before assigning defaults.
            $event = new Building($config);
            event($event);
            $config = $event->getConfiguration();

            $config = $this->bootStrategy($config);
            $config = $this->bootTokenCache($config);
            $config = $this->bootManagementTokenCache($config);

            if (in_array($config['strategy'], Configuration::STRATEGIES_USING_SESSIONS, true)) {
                $config = $this->bootSessionStorage($config);
                $config = $this->bootTransientStorage($config);
            }

            $config = new Configuration($config);

            // Give host application an opportunity to update the configuration before applying it.
            $event = new Built($config);
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
        if (! $this->sdk instanceof SDKContract) {
            return $this->setSdk(new SDK($this->getConfiguration()));
        }

        return $this->sdk;
    }

    public function management(): ManagementInterface
    {
        return $this->getSdk()->management();
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

        if ($this->sdk instanceof SDKContract) {
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
