<?php

declare(strict_types=1);

namespace Auth0\Laravel\Entities;

use Auth0\Laravel\Cache\LaravelCachePool;
use Auth0\Laravel\Event\Configuration\{Building, Built};
use Auth0\Laravel\Store\LaravelSession;
use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration as SdkConfiguration;
use Auth0\SDK\Contract\{Auth0Interface as Sdk, StoreInterface};
use Auth0\SDK\Contract\API\ManagementInterface;
use Auth0\SDK\Utility\HttpTelemetry;
use Psr\Cache\CacheItemPoolInterface;

use function in_array;
use function is_string;

class Configuration implements ConfigurationContract
{
    public function __construct(
        private ?Sdk $sdk = null,
        private ?SdkConfiguration $configuration = null,
        private ?CacheItemPoolInterface $tokenCachePool = null,
        private ?CacheItemPoolInterface $managementTokenCachePool = null,
    ) {
    }

    public static function create(
        SdkConfiguration | array | null $configuration = null
    ) {
        $instance = new static();
        $instance->setConfiguration($configuration);

        return $instance;
    }

    public function getConfiguration(): SdkConfiguration
    {
        if (! $this->configuration instanceof SdkConfiguration) {
            $this->configuration = $this->createConfiguration(config('auth0.default') ?? []);
        }

        return $this->configuration;
    }

    private function createConfiguration(
        array $configuration
    ): SdkConfiguration {
        // Give host application an opportunity to update the configuration before building configuration.
        $event = new Building($configuration);
        event($event);
        $configuration = $event->getConfiguration();

        $configuration = $this->bootStrategy($configuration);
        $configuration = $this->bootTokenCache($configuration);
        $configuration = $this->bootManagementTokenCache($configuration);

        if (in_array($configuration['strategy'], SdkConfiguration::STRATEGIES_USING_SESSIONS, true)) {
            $configuration = $this->bootSessionStorage($configuration);
            $configuration = $this->bootTransientStorage($configuration);
        }

        $configuration = new SdkConfiguration($configuration);

        // Give host application an opportunity to update the configuration before applying it.
        $event = new Built($configuration);
        event($event);
        return $event->getConfiguration();
    }

    public function getCredentials(): ?object
    {
        return $this->getSdk()->getCredentials();
    }

    public function getSdk(): Sdk
    {
        if (! $this->sdk instanceof Sdk) {
            return $this->setSdk(new Auth0($this->getConfiguration()));
        }

        return $this->sdk;
    }

    public function management(): ManagementInterface
    {
        // dd($this->getConfiguration());
        return $this->getSdk()->management();
    }

    public function reset(): self
    {
        unset($this->sdk, $this->configuration);

        $this->sdk = null;
        $this->configuration = null;

        return $this;
    }

    public function setConfiguration(
        SdkConfiguration | array | null $configuration = null
    ): self {
        if (is_array($configuration)) {
            $configuration = $this->createConfiguration($configuration);
        }

        $this->configuration = $configuration;

        if ($this->sdk instanceof Sdk) {
            $this->sdk->setConfiguration($configuration);
        }

        return $this;
    }

    public function setSdk(Sdk $sdk): Sdk
    {
        $this->configuration = $sdk->configuration();
        $this->sdk = $sdk;

        $this->setSdkTelemetry();

        return $this->sdk;
    }

    private function bootManagementTokenCache(array $config): array
    {
        $managementTokenCache = $config['managementTokenCache'] ?? null;

        // if (false === $managementTokenCache) {
        //     unset($config['managementTokenCache']);

        //     return $config;
        // }

        // if (null === $managementTokenCache) {
        //     $managementTokenCache = $this->getManagementTokenCachePool();
        // }

        // if (is_string($managementTokenCache)) {
        //     $managementTokenCache = app(trim($managementTokenCache));
        // }

        $config['managementTokenCache'] = $managementTokenCache instanceof CacheItemPoolInterface ? $managementTokenCache : null;

        return $config;
    }

    private function bootSessionStorage(array $config): array
    {
        $sessionStorage = $config['sessionStorage'] ?? null;
        $sessionStorageId = $config['sessionStorageId'] ?? 'auth0_session';

        if (false === $sessionStorage) {
            unset($config['sessionStorage']);

            return $config;
        }

        if (null === $sessionStorage) {
            $sessionStorage = app(LaravelSession::class, [
                'prefix' => $sessionStorageId,
            ]);
        }

        if (is_string($sessionStorage)) {
            $sessionStorage = app(trim($sessionStorage), [
                'prefix' => $sessionStorageId,
            ]);
        }

        $config['sessionStorage'] = $sessionStorage instanceof StoreInterface ? $sessionStorage : null;

        return $config;
    }

    private function bootStrategy(array $config): array
    {
        $strategy = $config['strategy'] ?? SdkConfiguration::STRATEGY_REGULAR;

        if (! is_string($strategy)) {
            $strategy = SdkConfiguration::STRATEGY_REGULAR;
        }

        $config['strategy'] = $strategy;

        return $config;
    }

    private function bootTokenCache(array $config): array
    {
        $tokenCache = $config['tokenCache'] ?? null;

        if (false === $tokenCache) {
            unset($config['tokenCache']);

            return $config;
        }

        if (null === $tokenCache) {
            $tokenCache = $this->getTokenCachePool();
        }

        if (is_string($tokenCache)) {
            $tokenCache = app(trim($tokenCache));
        }

        $config['tokenCache'] = $tokenCache instanceof CacheItemPoolInterface ? $tokenCache : null;

        return $config;
    }

    private function bootTransientStorage(array $config): array
    {
        $transientStorage = $config['transientStorage'] ?? null;
        $transientStorageId = $config['transientStorageId'] ?? 'auth0_transient';

        if (false === $transientStorage) {
            unset($config['transientStorage']);

            return $config;
        }

        if (null === $transientStorage) {
            $transientStorage = app(LaravelSession::class, [
                'prefix' => $transientStorageId,
            ]);
        }

        if (is_string($transientStorage)) {
            $transientStorage = app(trim($transientStorage), [
                'prefix' => $transientStorageId,
            ]);
        }

        $config['transientStorage'] = $transientStorage instanceof StoreInterface ? $transientStorage : null;

        return $config;
    }

    private function getManagementTokenCachePool(): CacheItemPoolInterface
    {
        if (! $this->managementTokenCachePool instanceof CacheItemPoolInterface) {
            $this->managementTokenCachePool = app(LaravelCachePool::class);
        }

        return $this->managementTokenCachePool;
    }

    private function getTokenCachePool(): CacheItemPoolInterface
    {
        if (! $this->tokenCachePool instanceof CacheItemPoolInterface) {
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
        HttpTelemetry::setPackage('laravel-auth0', Auth0::VERSION);

        return $this;
    }
}
