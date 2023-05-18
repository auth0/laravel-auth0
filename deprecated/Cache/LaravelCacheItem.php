<?php

declare(strict_types=1);

namespace Auth0\Laravel\Cache;

use Auth0\Laravel\Bridges\{CacheItemBridgeAbstract, CacheItemBridgeContract};
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;

use function is_int;

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Bridges\CacheItemBridge instead.
 *
 * @internal
 *
 * @api
 */
final class LaravelCacheItem extends CacheItemBridgeAbstract implements CacheItemBridgeContract
{
    public function expiresAfter(int | DateInterval | null $time): static
    {
        $this->expiration = match (true) {
            null === $time => new DateTimeImmutable('now +1 year'),
            is_int($time) => new DateTimeImmutable('now +' . (string) $time . ' seconds'),
            $time instanceof DateInterval => (new DateTimeImmutable())->add($time),
        };

        return $this;
    }

    public function expiresAt(?DateTimeInterface $expiration): static
    {
        $this->expiration = $expiration ?? new DateTimeImmutable('now +1 year');

        return $this;
    }

    public function set(mixed $value): static
    {
        $this->value = $value;

        return $this;
    }

    public static function miss(string $key): self
    {
        return new self(
            key: $key,
            value: null,
            hit: false,
        );
    }
}
