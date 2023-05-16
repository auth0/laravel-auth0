<?php

declare(strict_types=1);

namespace Auth0\Laravel\Bridges;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;

use function is_int;

/**
 * Bridges the Laravel's Cache API with the PSR-6's CacheItemInterface interface.
 *
 * @internal
 *
 * @api
 */
final class CacheItemBridge extends CacheItemBridgeAbstract implements CacheItemBridgeContract
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
