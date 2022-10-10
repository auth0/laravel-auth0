<?php

declare(strict_types=1);

namespace Auth0\Laravel\Store;

use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Contract\StoreInterface;
use Exception;

/**
 * Class LaravelSession
 * Bridges the Auth0-PHP SDK StoreInterface with the Laravel Session Store API.
 */
final class LaravelSession implements StoreInterface
{
    /**
     * Instance of SdkConfiguration, for shared configuration across classes.
     */
    // @phpstan-ignore-next-line
    private SdkConfiguration $configuration;

    /**
     * Session base name, configurable on instantiation.
     */
    private string $sessionPrefix;

    /**
     * Track if a bootup event has been sent out yet.
     */
    private bool $booted = false;

    /**
     * Psr14Store constructor.
     *
     * @param SdkConfiguration $configuration Base configuration options for the SDK. See the SdkConfiguration class constructor for options.
     * @param string           $sessionPrefix A string to prefix session keys with.
     */
    public function __construct(SdkConfiguration $configuration, string $sessionPrefix = 'auth0')
    {
        $this->configuration = $configuration;
        $this->sessionPrefix = $sessionPrefix;
    }

    /**
     * Dispatch event to toggle state deferrance.
     *
     * @param bool $deferring Whether to defer persisting the storage state.
     */
    public function defer(bool $deferring): void
    {
        return;
    }

    /**
     * Dispatch event to set the value of a key-value pair.
     *
     * @param string $key   Session key to set.
     * @param mixed  $value Value to use.
     */
    public function set(string $key, $value): void
    {
        $this->boot();
        $this->getStore()
            ->put($this->getPrefixedKey($key), $value);
    }

    /**
     * Dispatch event to retrieve the value of a key-value pair.
     *
     * @param string $key     Session key to query.
     * @param mixed  $default Default to return if nothing was found.
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $this->boot();
        return $this->getStore()
            ->get($this->getPrefixedKey($key), $default);
    }

    /**
     * Dispatch event to clear all key-value pairs.
     */
    public function purge(): void
    {
        $this->boot();

        // It would be unwise for us to simply flush() a session here, as it is shared with the app ecosystem.
        // Instead, iterate through the session data, and if they key is prefixed with our assigned string, delete it.

        $pairs = $this->getStore()
            ->all();
        $prefix = $this->sessionPrefix . '_';

        foreach (array_keys($pairs) as $key) {
            if (substr($key, 0, strlen($prefix)) === $prefix) {
                $this->delete($key);
            }
        }
    }

    /**
     * Dispatch event to delete key-value pair.
     *
     * @param string $key Session key to delete.
     */
    public function delete(string $key): void
    {
        $this->boot();
        $this->getStore()
            ->forget($this->getPrefixedKey($key));
    }

    /**
     * Dispatch event to alert that a session should be prepared for an incoming request.
     */
    private function boot(): void
    {
        if (! $this->booted) {
            if (! $this->getStore()->isStarted()) {
                $this->getStore()
                    ->start();
            }

            $this->booted = true;
        }

        return;
    }

    private function getStore(): \Illuminate\Session\Store
    {
        $request = request();

        if ($request instanceof \Illuminate\Http\Request) {
            return $request->session();
        }

        throw new Exception('A cache must be configured.');
    }

    private function getPrefixedKey(string $key): string
    {
        if ($this->sessionPrefix !== '') {
            return $this->sessionPrefix . '_' . $key;
        }

        return $key;
    }
}
