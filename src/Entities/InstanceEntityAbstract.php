<?php

declare(strict_types=1);

namespace Auth0\Laravel\Entities;

use Auth0\Laravel\Bridges\{CacheBridge, SessionBridge};
use Auth0\Laravel\{Configuration, Events, Service};
use Auth0\Laravel\Events\Configuration\{BuildingConfigurationEvent, BuiltConfigurationEvent};
use Auth0\SDK\API\Management\Wrapper\{ManagementClient, ManagementClientOptions};
use Auth0\SDK\Auth0;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Contract\{Auth0Interface, StoreInterface};
use Auth0\SDK\Utility\HttpTelemetry;
use Psr\Cache\CacheItemPoolInterface;

use function in_array;
use function is_array;
use function is_string;

/**
 * @api
 */
abstract class InstanceEntityAbstract extends EntityAbstract
{
    private ?ManagementClient $management = null;

    public function __construct(
        protected ?Auth0Interface $sdk = null,
        protected ?SdkConfiguration $configuration = null,
        protected ?CacheItemPoolInterface $tokenCachePool = null,
        protected ?CacheItemPoolInterface $managementTokenCachePool = null,
        protected ?string $guardConfigurationKey = null,
        protected ?CacheItemPoolInterface $backchannelLogoutCachePool = null,
    ) {
    }

    final public function getConfiguration(): SdkConfiguration
    {
        if (! $this->configuration instanceof SdkConfiguration) {
            $configuration = [];

            if (2 === Configuration::version()) {
                $defaultConfiguration = config('auth0.guards.default');
                $guardConfiguration = [];

                if (null !== $this->guardConfigurationKey && '' !== $this->guardConfigurationKey && 'default' !== $this->guardConfigurationKey) {
                    $guardConfiguration = config('auth0.guards.' . $this->guardConfigurationKey) ?? [];
                }

                if (is_array($defaultConfiguration) && [] !== $defaultConfiguration) {
                    $configuration = array_merge($configuration, array_filter($defaultConfiguration));
                }

                if (is_array($guardConfiguration) && [] !== $guardConfiguration) {
                    $configuration = array_merge($configuration, array_filter($guardConfiguration));
                }
            }

            if (2 !== Configuration::version()) {
                $configuration = config('auth0');

                if (! is_array($configuration)) {
                    $configuration = [];
                }
            }

            $this->configuration = $this->createConfiguration($configuration);
        }

        return $this->configuration;
    }

    final public function getCredentials(): ?object
    {
        return $this->getSdk()->getCredentials();
    }

    final public function getGuardConfigurationKey(): ?string
    {
        return $this->guardConfigurationKey;
    }

    final public function getSdk(): Auth0Interface
    {
        if (! $this->sdk instanceof Auth0Interface) {
            return $this->setSdk(new Auth0($this->getConfiguration()));
        }

        return $this->sdk;
    }

    /**
     * Return a v9 Management API client built from this instance's configuration.
     *
     * Returns the base Auth0-PHP `ManagementClient`, so the full SDK surface is
     * available: sub-clients by property (e.g. `->users`, `->clients`), the raw
     * generated client via `getManagement()`, and so on. The token is resolved
     * automatically — a configured `managementToken` if present, otherwise a
     * client-credentials token fetched and cached via the management token cache.
     *
     * @param array<string, mixed> $options Overrides passed to ManagementClientOptions.
     *                                      Recognized keys: token, audience, httpClient,
     *                                      timeout, maxRetries, additionalHeaders, tokenCache.
     *                                      Merged over defaults from config and this
     *                                      instance's SdkConfiguration.
     */
    final public function management(array $options = []): ManagementClient
    {
        // Cache the no-options client so repeated management() calls in a request
        // reuse one instance (and its token cache) instead of rebuilding.
        if ([] === $options && $this->management instanceof ManagementClient) {
            return $this->management;
        }

        $configuration = $this->getConfiguration();
        $options = array_merge($this->getManagementDefaults(), $options);

        $management = new ManagementClient(new ManagementClientOptions(
            domain: (string) $configuration->getDomain(),
            token: $options['token'] ?? $configuration->getManagementToken(),
            clientId: $configuration->getClientId(),
            clientSecret: $configuration->getClientSecret(),
            audience: $options['audience'] ?? null,
            httpClient: $options['httpClient'] ?? null,
            timeout: $options['timeout'] ?? null,
            maxRetries: $options['maxRetries'] ?? null,
            additionalHeaders: $options['additionalHeaders'] ?? null,
            tokenCache: $options['tokenCache'] ?? $configuration->getManagementTokenCache(),
        ));

        if ([] === $options) {
            $this->management = $management;
        }

        return $management;
    }

    final public function setGuardConfigurationKey(
        ?string $guardConfigurationKey = null,
    ): self {
        $this->guardConfigurationKey = $guardConfigurationKey;

        return $this;
    }

    final public function setSdk(Auth0Interface $sdk): Auth0Interface
    {
        $this->configuration = $sdk->configuration();
        $this->sdk = $sdk;
        $this->resetManagementClient();

        $this->setSdkTelemetry();

        return $sdk;
    }

    abstract public function reset(): self;

    /**
     * @param null|array<string>|SdkConfiguration $configuration
     */
    abstract public function setConfiguration(
        SdkConfiguration | array | null $configuration = null,
    ): self;

    /**
     * Resolve default management options: the global `auth0.management` block,
     * with a per-guard `auth0.guards.<key>.management` block merged on top.
     */
    private function getManagementDefaults(): array
    {
        $global = config('auth0.management');
        $defaults = is_array($global) ? $global : [];

        $key = $this->guardConfigurationKey;

        if (null !== $key && '' !== $key && 'default' !== $key) {
            $guard = config('auth0.guards.' . $key . '.management');

            if (is_array($guard)) {
                $defaults = array_merge($defaults, $guard);
            }
        }

        return $defaults;
    }

    protected function bootBackchannelLogoutCache(array $config): array
    {
        $backchannelLogoutCache = $config['backchannelLogoutCache'] ?? null;

        if (false === $backchannelLogoutCache) {
            unset($config['backchannelLogoutCache']);

            return $config;
        }

        if (null === $backchannelLogoutCache) {
            $backchannelLogoutCache = $this->getBackchannelLogoutCachePool();
        }

        if (is_string($backchannelLogoutCache)) {
            $backchannelLogoutCache = app(trim($backchannelLogoutCache));
        }

        $config['backchannelLogoutCache'] = $backchannelLogoutCache instanceof CacheItemPoolInterface ? $backchannelLogoutCache : null;

        return $config;
    }

    protected function bootManagementTokenCache(array $config): array
    {
        $managementTokenCache = $config['managementTokenCache'] ?? null;
        $this->getManagementTokenCachePool();

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

    protected function bootSessionStorage(array $config): array
    {
        $sessionStorage = $config['sessionStorage'] ?? null;
        $sessionStorageId = $config['sessionStorageId'] ?? 'auth0_session';

        if (false === $sessionStorage || 'cookie' === $sessionStorage) {
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

    protected function bootStrategy(array $config): array
    {
        $strategy = $config['strategy'] ?? SdkConfiguration::STRATEGY_REGULAR;

        if (! is_string($strategy)) {
            $strategy = SdkConfiguration::STRATEGY_REGULAR;
        }

        $config['strategy'] = $strategy;

        return $config;
    }

    protected function bootTokenCache(array $config): array
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

    protected function bootTransientStorage(array $config): array
    {
        $transientStorage = $config['transientStorage'] ?? null;
        $transientStorageId = $config['transientStorageId'] ?? 'auth0_transient';

        if (false === $transientStorage || 'cookie' === $transientStorage) {
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

    protected function createConfiguration(
        array $configuration,
    ): SdkConfiguration {
        Events::dispatch(new BuildingConfigurationEvent($configuration));

        $configuration = $this->bootStrategy($configuration);
        $configuration = $this->bootTokenCache($configuration);
        $configuration = $this->bootManagementTokenCache($configuration);

        if (in_array($configuration['strategy'], SdkConfiguration::STRATEGIES_USING_SESSIONS, true)) {
            $configuration = $this->bootSessionStorage($configuration);
            $configuration = $this->bootTransientStorage($configuration);
        }

        $sdkConfiguration = new SdkConfiguration($configuration);

        Events::dispatch(new BuiltConfigurationEvent($sdkConfiguration));

        return $sdkConfiguration;
    }

    protected function getBackchannelLogoutCachePool(): CacheItemPoolInterface
    {
        if (! $this->backchannelLogoutCachePool instanceof CacheItemPoolInterface) {
            $this->backchannelLogoutCachePool = app(CacheBridge::class);
        }

        return $this->backchannelLogoutCachePool;
    }

    protected function getManagementTokenCachePool(): CacheItemPoolInterface
    {
        if (! $this->managementTokenCachePool instanceof CacheItemPoolInterface) {
            $this->managementTokenCachePool = app(CacheBridge::class);
        }

        return $this->managementTokenCachePool;
    }

    protected function getTokenCachePool(): CacheItemPoolInterface
    {
        if (! $this->tokenCachePool instanceof CacheItemPoolInterface) {
            $this->tokenCachePool = app(CacheBridge::class);
        }

        return $this->tokenCachePool;
    }

    /**
     * Clear the cached Management client so the next management() call rebuilds
     * it from current configuration.
     */
    final protected function resetManagementClient(): void
    {
        $this->management = null;
    }

    /**
     * Updates the Auth0 PHP SDK's telemetry to include the correct Laravel markers.
     */
    protected function setSdkTelemetry(): self
    {
        HttpTelemetry::setEnvProperty('Laravel', app()->version());
        HttpTelemetry::setPackage('laravel', Service::VERSION);

        return $this;
    }
}
