<?php

declare(strict_types=1);

namespace Auth0\Laravel\Bridges;

use Auth0\Laravel\Exceptions\SessionException;
use Illuminate\Session\Store;
use InvalidArgumentException;

use function array_key_exists;
use function is_array;
use function is_string;

/**
 * @api
 */
abstract class SessionBridgeAbstract extends BridgeAbstract
{
    public function __construct(
        protected string $prefix = 'auth0',
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
        $payload = $this->getPayload() ?? [];

        if (array_key_exists($key, $payload)) {
            unset($payload[$key]);
            $this->getStore()->put($this->getPrefix(), json_encode(array_filter($payload), JSON_THROW_ON_ERROR));
        }
    }

    /**
     * Retrieve a value from the Laravel session by key. (Key will be automatically prefixed with the SDK's configured namespace.).
     *
     * @param string $key     session key to query
     * @param mixed  $default default to return if nothing was found
     *
     * @throws SessionException If a Laravel session store is not available.
     */
    final public function get(string $key, $default = null): mixed
    {
        $payload = $this->getPayload() ?? [];

        return $payload[$key] ?? $default;
    }

    /**
     * Get all values from the Laravel session that are prefixed with the SDK's configured namespace.
     *
     * @throws SessionException If a Laravel session store is not available.
     */
    final public function getAll(): array
    {
        return $this->getPayload() ?? [];
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
        $this->getStore()->forget($this->getPrefix());
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
        $payload = $this->getPayload() ?? [];
        $payload[$key] = $value;

        $this->getStore()->put($this->getPrefix(), json_encode($payload, JSON_THROW_ON_ERROR));
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

    protected function getPayload(): ?array
    {
        $encoded = $this->getStore()->get($this->getPrefix());

        if (is_string($encoded)) {
            $decoded = json_decode($encoded, true, 512);

            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * Retrieves the Laravel session store.
     *
     * @throws SessionException If a Laravel session store is not available.
     *
     * @psalm-suppress RedundantConditionGivenDocblockType
     */
    protected function getStore(): Store
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
