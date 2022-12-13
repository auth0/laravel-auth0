<?php

declare(strict_types=1);

namespace Auth0\Laravel\Store;

use Auth0\SDK\Contract\StoreInterface;
use Exception;

/**
 * Class LaravelSession
 * Bridges the Auth0-PHP SDK StoreInterface with the Laravel Session Store API.
 */
final class LaravelSession implements StoreInterface
{
    public function __construct(
        private string $prefix = 'auth0',
        private bool $booted = false,
    ) {
    }

    /**
     * Dispatch event to toggle state deferrance.
     *
     * @param  bool  $deferring  whether to defer persisting the storage state
     */
    public function defer(bool $deferring): void
    {
    }

    /**
     * Dispatch event to set the value of a key-value pair.
     *
     * @param  string  $key  session key to set
     * @param  mixed  $value  value to use
     */
    public function set(string $key, $value): void
    {
        $this->boot();
        $this->getStore()->
            put($this->getPrefixedKey($key), $value);
    }

    /**
     * Dispatch event to retrieve the value of a key-value pair.
     *
     * @param  string  $key  session key to query
     * @param  mixed  $default  default to return if nothing was found
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        $this->boot();

        return $this->getStore()->
            get($this->getPrefixedKey($key), $default);
    }

    /**
     * Dispatch event to clear all key-value pairs.
     */
    public function purge(): void
    {
        $this->boot();

        // It would be unwise for us to simply flush() a session here, as it is shared with the app ecosystem.
        // Instead, iterate through the session data, and if they key is prefixed with our assigned string, delete it.

        $pairs = $this->getStore()->
            all();
        $prefix = $this->prefix . '_';

        foreach (array_keys($pairs) as $key) {
            if (mb_substr($key, 0, mb_strlen($prefix)) === $prefix) {
                $this->delete($key);
            }
        }
    }

    /**
     * Dispatch event to delete key-value pair.
     *
     * @param  string  $key  session key to delete
     */
    public function delete(string $key): void
    {
        $this->boot();
        $this->getStore()->
            forget($this->getPrefixedKey($key));
    }

    /**
     * Dispatch event to alert that a session should be prepared for an incoming request.
     */
    private function boot(): void
    {
        if (! $this->booted) {
            if (! $this->getStore()->isStarted()) {
                $this->getStore()->
                    start();
            }

            $this->booted = true;
        }
    }

    /**
     *  {@inheritdoc}
     *
     * @psalm-suppress RedundantConditionGivenDocblockType
     */
    private function getStore(): \Illuminate\Session\Store
    {
        $request = request();

        // @phpstan-ignore-next-line
        if ($request instanceof \Illuminate\Http\Request) {
            return $request->session();
        }

        // @phpstan-ignore-next-line
        throw new Exception('A cache must be configured.');
    }

    private function getPrefixedKey(string $key): string
    {
        if ('' !== $this->prefix) {
            return $this->prefix . '_' . $key;
        }

        return $key;
    }
}
