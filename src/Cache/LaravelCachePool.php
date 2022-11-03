<?php

declare(strict_types=1);

namespace Auth0\Laravel\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class LaravelCachePool
 * This class provides a bridge between Laravel's cache API and PSR-6/PSR-16.
 */
final class LaravelCachePool implements CacheItemPoolInterface
{
    /**
     * @var array<array{item: CacheItemInterface, expiration: \DateTimeInterface|int|null}>
     */
    private array $deferred = [];

    public function getItem(string $key): CacheItemInterface
    {
        $value = $this->getStore()->
            get($key);

        if (false === $value) {
            return LaravelCacheItem::miss($key);
        }

        return $this->createItem($key, $value);
    }

    /**
     * @param  string[]  $keys
     * @return CacheItemInterface[]
     */
    public function getItems(array $keys = []): iterable
    {
        if ([] === $keys) {
            return [];
        }

        $results = $this->getStore()->
            many($keys);
        $items = [];

        foreach ($results as $key => $value) {
            $key = (string) $key;
            $items[$key] = $this->createItem($key, $value);
        }

        return $items;
    }

    /**
     * @param  string  $key  the key for which to return the corresponding Cache Item
     */
    public function hasItem(mixed $key): bool
    {
        return $this->getItem($key)->
            isHit();
    }

    public function clear(): bool
    {
        $this->deferred = [];

        return $this->getStore()->
            flush();
    }

    /**
     * @param  string  $key  the key for which to return the corresponding Cache Item
     */
    public function deleteItem(mixed $key): bool
    {
        return $this->getStore()->
            forget($key);
    }

    public function deleteItems(array $keys): bool
    {
        $deleted = true;

        foreach ($keys as $key) {
            if (! $this->deleteItem($key)) {
                $deleted = false;
            }
        }

        return $deleted;
    }

    public function save(CacheItemInterface $item): bool
    {
        if (! $item instanceof LaravelCacheItem) {
            return false;
        }

        $value = serialize($item->get());
        $key = $item->getKey();
        $expires = $item->getExpiration();

        if ($expires->getTimestamp() <= time()) {
            return $this->deleteItem($key);
        }

        $ttl = $expires->getTimestamp() - time();

        return $this->getStore()->put($key, $value, $ttl);
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        if (! $item instanceof LaravelCacheItem) {
            return false;
        }

        $this->deferred[$item->getKey()] = [
            'item'       => $item,
            'expiration' => $item->getExpiration(),
        ];

        return true;
    }

    public function commit(): bool
    {
        $success = true;

        foreach (array_keys($this->deferred) as $singleDeferred) {
            $item = $this->getDeferred((string) $singleDeferred);

            if (null !== $item && ! $this->save($item)) {
                $success = false;
            }
        }

        $this->deferred = [];

        return $success;
    }

    private function getStore(): \Illuminate\Contracts\Cache\Store
    {
        return app(\Illuminate\Cache\CacheManager::class)->getStore();
    }

    private function createItem(string $key, mixed $value): CacheItemInterface
    {
        if (! \is_string($value)) {
            return LaravelCacheItem::miss($key);
        }

        $value = unserialize($value);

        if (false === $value) {
            return LaravelCacheItem::miss($key);
        }

        return new LaravelCacheItem($key, $value, true);
    }

    private function getDeferred(string $key): ?CacheItemInterface
    {
        if (! isset($this->deferred[$key])) {
            return null;
        }

        $deferred = $this->deferred[$key];
        $item = clone $deferred['item'];
        $expires = $deferred['expiration'];

        if (null !== $expires && $expires <= time()) {
            unset($this->deferred[$key]);

            return null;
        }

        return $item;
    }
}
