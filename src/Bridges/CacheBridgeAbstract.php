<?php

declare(strict_types=1);

namespace Auth0\Laravel\Bridges;

use DateTimeInterface;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Cache\Store;
use Psr\Cache\CacheItemInterface;

use RuntimeException;

use function is_string;

/**
 * @api
 */
abstract class CacheBridgeAbstract extends BridgeAbstract
{
    /**
     * @var array<array{item: CacheItemInterface, expiration: null|DateTimeInterface|int}>
     */
    protected array $deferred = [];

    final public function clear(): bool
    {
        $this->deferred = [];

        return $this->getCache()->flush();
    }

    final public function commit(): bool
    {
        $success = true;

        foreach (array_keys($this->deferred) as $singleDeferred) {
            $item = $this->getDeferred((string) $singleDeferred);

            // @codeCoverageIgnoreStart
            if ($item instanceof CacheItemInterface && ! $this->save($item)) {
                $success = false;
            }
            // @codeCoverageIgnoreEnd
        }

        $this->deferred = [];

        return $success;
    }

    /**
     * @param string $key the key for which to return the corresponding Cache Item
     */
    final public function deleteItem(string $key): bool
    {
        return $this->getCache()->forget($key);
    }

    final public function deleteItems(array $keys): bool
    {
        $deleted = true;

        foreach ($keys as $key) {
            if (! $this->deleteItem($key)) {
                $deleted = false;
            }
        }

        return $deleted;
    }

    final public function getItem(string $key): CacheItemInterface
    {
        $value = $this->getCache()->get($key);

        if (false === $value) {
            return CacheItemBridge::miss($key);
        }

        return $this->createItem($key, $value);
    }

    /**
     * @param string[] $keys
     *
     * @return CacheItemInterface[]
     */
    final public function getItems(array $keys = []): iterable
    {
        if ([] === $keys) {
            return [];
        }

        $results = $this->getCache()->many($keys);
        $items = [];

        foreach ($results as $key => $value) {
            $key = (string) $key;
            $items[$key] = $this->createItem($key, $value);
        }

        return $items;
    }

    /**
     * @param string $key the key for which to return the corresponding Cache Item
     */
    final public function hasItem(string $key): bool
    {
        return $this->getItem($key)
            ->isHit();
    }

    final public function save(CacheItemInterface $item): bool
    {
        if (! $item instanceof CacheItemBridge) {
            return false;
        }

        $value = serialize($item->getRawValue());
        $key = $item->getKey();
        $expires = $item->getExpiration();

        if ($expires->getTimestamp() <= time()) {
            return $this->deleteItem($key);
        }

        $ttl = $expires->getTimestamp() - time();

        return $this->getCache()->put($key, $value, $ttl);
    }

    final public function saveDeferred(CacheItemInterface $item): bool
    {
        if (! $item instanceof CacheItemBridge) {
            return false;
        }

        $this->deferred[$item->getKey()] = [
            'item' => $item,
            'expiration' => $item->getExpiration(),
        ];

        return true;
    }

    protected function createItem(string $key, mixed $value): CacheItemInterface
    {
        if (! is_string($value)) {
            return CacheItemBridge::miss($key);
        }

        $value = unserialize($value);

        if (false === $value) {
            return CacheItemBridge::miss($key);
        }

        return new CacheItemBridge($key, $value, true);
    }

    protected function getCache(): Store
    {
        $cache = cache();

        // @codeCoverageIgnoreStart
        if (! $cache instanceof CacheManager) {
            throw new RuntimeException('Cache store is not an instance of Illuminate\Contracts\Cache\CacheManager');
        }
        // @codeCoverageIgnoreEnd

        return $cache->getStore();
    }

    /**
     * @param string $key the key for which to return the corresponding Cache Item
     *
     * @codeCoverageIgnore
     */
    protected function getDeferred(string $key): ?CacheItemInterface
    {
        if (! isset($this->deferred[$key])) {
            return null;
        }

        $deferred = $this->deferred[$key];
        $item = clone $deferred['item'];
        $expires = $deferred['expiration'];

        if ($expires instanceof DateTimeInterface) {
            $expires = $expires->getTimestamp();
        }

        if (null !== $expires && $expires <= time()) {
            unset($this->deferred[$key]);

            return null;
        }

        return $item;
    }
}
