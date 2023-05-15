<?php

declare(strict_types=1);

namespace Auth0\Laravel\Bridges;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;

use function is_int;

/**
 * @api
 */
abstract class CacheItemBridgeAbstract implements CacheItemBridgeContract
{
    public function __construct(
        private string $key,
        private mixed $value,
        private bool $hit,
        private ?DateTimeInterface $expiration = null,
    ) {
    }

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

    public function get(): mixed
    {
        return $this->isHit() ? $this->value : null;
    }

    /**
     * Returns the expiration timestamp.
     */
    public function getExpiration(): DateTimeInterface
    {
        return $this->expiration ?? new DateTimeImmutable('now +1 year');
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Returns the raw value, regardless of hit status.
     */
    public function getRawValue(): mixed
    {
        return $this->value;
    }

    public function isHit(): bool
    {
        return $this->hit;
    }

    public function set(mixed $value): static
    {
        $this->value = $value;

        return $this;
    }
}
