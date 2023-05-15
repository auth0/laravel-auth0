<?php

declare(strict_types=1);

namespace Auth0\Laravel\Bridges;

use Auth0\Laravel\Exceptions\SessionException;
use Illuminate\Session\Store;
use InvalidArgumentException;

/**
 * @api
 */
abstract class SessionBridgeAbstract implements SessionBridgeContract
{
    private string $prefix = 'auth0';

    public function __construct(
        string $prefix = 'auth0',
    ) {
        $this->setPrefix($prefix);
    }

    /**
     * This method is required by the interface but is not used by this SDK.
     *
     * @param bool $deferring whether to defer persisting the storage state
     */
    final public function defer(bool $deferring): void
    {
    }

    /**
     * Delete a value from the Laravel session by key. (Key will be automatically prefixed with the SDK's configured namespace.).
     *
     * @param string $key session key to delete
     *
     * @throws SessionException If a Laravel session store is not available.
     */
    final public function delete(string $key): void
    {
        $this->getStore()->forget($this->getPrefixedKey($key));
    }

    /**
     * Retrieve a value from the Laravel session by key. (Key will be automatically prefixed with the SDK's configured namespace.).
     *
     * @param string $key     session key to query
     * @param mixed  $default default to return if nothing was found
     *
     * @throws SessionException If a Laravel session store is not available.
     */
    final public function get(string $key, $default = null)
    {
        return $this->getStore()->get($this->getPrefixedKey($key), $default);
    }

    /**
     * Get all values from the Laravel session that are prefixed with the SDK's configured namespace.
     *
     * @throws SessionException If a Laravel session store is not available.
     */
    final public function getAll(): array
    {
        $pairs = $this->getStore()->all();
        $prefix = $this->prefix . '_';
        $response = [];

        foreach (array_keys($pairs) as $key) {
            if (mb_substr($key, 0, mb_strlen($prefix)) === $prefix) {
                $response[$key] = $pairs[$key];
            }
        }

        return $response;
    }

    /**
     * Get the prefix used for all session keys.
     *
     * @return string Prefix used for all session keys.
     */
    final public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * Delete all values from the Laravel session that are prefixed with the SDK's configured namespace.
     *
     * @throws SessionException If a Laravel session store is not available.
     */
    final public function purge(): void
    {
        $entities = $this->getAll();

        foreach (array_keys($entities) as $entity) {
            $this->getStore()->forget($entity);
        }
    }

    /**
     * Store a value in the Laravel session. (Key will be automatically prefixed with the SDK's configured namespace.).
     *
     * @param string $key   session key to set
     * @param mixed  $value value to use
     *
     * @throws SessionException If a Laravel session store is not available.
     */
    final public function set(string $key, $value): void
    {
        $this->getStore()->put($this->getPrefixedKey($key), $value);
    }

    /**
     * Set the prefix used for all session keys.
     *
     * @param string $prefix Prefix to use for all session keys.
     *
     * @return $this
     */
    final public function setPrefix(
        string $prefix = 'auth0',
    ): self {
        $prefix = trim($prefix);

        if ('' === $prefix) {
            throw new InvalidArgumentException('Prefix cannot be empty.');
        }

        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Prefixes a key with the SDK's configured namespace.
     *
     * @param string $key
     */
    private function getPrefixedKey(string $key): string
    {
        return $this->getPrefix() . '_' . trim($key);
    }

    /**
     * Retrieves the Laravel session store.
     *
     * @throws SessionException If a Laravel session store is not available.
     *
     * @psalm-suppress RedundantConditionGivenDocblockType
     */
    private function getStore(): Store
    {
        $store = app('session.store');
        $request = app('request');

        if (! $request->hasSession(true)) {
            $request->setLaravelSession($store);
        }

        if (! $store->isStarted()) {
            $store->start();
        }

        return $store;
    }
}
