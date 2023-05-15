<?php

declare(strict_types=1);

namespace Auth0\Laravel\Entities;

use Auth0\Laravel\Bridges\{CacheBridge, SessionBridge};
use Auth0\Laravel\Events\Configuration\{BuildingConfigurationEvent, BuiltConfigurationEvent};
use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Contract\API\ManagementInterface;
use Auth0\SDK\Contract\{Auth0Interface, StoreInterface};
use Auth0\SDK\Utility\HttpTelemetry;
use Psr\Cache\CacheItemPoolInterface;

use function in_array;
use function is_array;
use function is_string;

/**
 * @api
 */
abstract class InstanceEntityAbstract implements InstanceEntityContract
{
    public function __construct(
        private ?Auth0Interface $sdk = null,
        private ?SdkConfiguration $configuration = null,
        private ?CacheItemPoolInterface $tokenCachePool = null,
        private ?CacheItemPoolInterface $managementTokenCachePool = null,
    ) {
    }

    final public function getConfiguration(): SdkConfiguration
    {
        if (! $this->configuration instanceof SdkConfiguration) {
            $configuration = config('auth0.default') ?? config('auth0') ?? [];
            $this->configuration = $this->createConfiguration($configuration);
        }

        return $this->configuration;
    }

    final public function getCredentials(): ?object
    {
        return $this->getSdk()->getCredentials();
    }

    final public function getSdk(): Auth0Interface
    {
        if (! $this->sdk instanceof Auth0Interface) {
            return $this->setSdk(new Auth0($this->getConfiguration()));
        }

        return $this->sdk;
    }

    final public function management(): ManagementInterface
    {
        return $this->getSdk()->management();
    }

    final public function reset(): self
    {
        unset($this->sdk, $this->configuration);

        $this->sdk = null;
        $this->configuration = null;

        return $this;
    }

    final public function setConfiguration(
        SdkConfiguration | array | null $configuration = null,
    ): self {
        if (is_array($configuration)) {
            $configuration = $this->createConfiguration($configuration);
        }

        $this->configuration = $configuration;

        if (null !== $this->configuration && $this->sdk instanceof Auth0Interface) {
            $this->sdk->setConfiguration($this->configuration);
        }

        return $this;
    }

    final public function setSdk(Auth0Interface $sdk): Auth0Interface
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
            $sessionStorage = app(SessionBridge::class, [
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
            $transientStorage = app(SessionBridge::class, [
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

    private function createConfiguration(
        array $configuration,
    ): SdkConfiguration {
        // ray($configuration);

        // Give host application an opportunity to update the configuration before building configuration.
        $event = new BuildingConfigurationEvent($configuration);
        event($event);
        $configuration = $event->getConfiguration();

        $configuration = $this->bootStrategy($configuration);
        $configuration = $this->bootTokenCache($configuration);
        $configuration = $this->bootManagementTokenCache($configuration);

        if (in_array($configuration['strategy'], SdkConfiguration::STRATEGIES_USING_SESSIONS, true)) {
            $configuration = $this->bootSessionStorage($configuration);
            $configuration = $this->bootTransientStorage($configuration);
        }

        // ray($configuration);

        $configuration = new SdkConfiguration($configuration);

        // Give host application an opportunity to update the configuration before applying it.
        $event = new BuiltConfigurationEvent($configuration);
        event($event);

        return $event->getConfiguration();
    }

    private function getManagementTokenCachePool(): CacheItemPoolInterface
    {
        if (! $this->managementTokenCachePool instanceof CacheItemPoolInterface) {
            $this->managementTokenCachePool = app(CacheBridge::class);
        }

        return $this->managementTokenCachePool;
    }

    private function getTokenCachePool(): CacheItemPoolInterface
    {
        if (! $this->tokenCachePool instanceof CacheItemPoolInterface) {
            $this->tokenCachePool = app(CacheBridge::class);
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
