<?php

declare(strict_types=1);

namespace Auth0\Laravel\Bridges;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;

/**
 * @api
 */
abstract class CacheItemBridgeAbstract extends BridgeAbstract
{
    public function __construct(
        protected string $key,
        protected mixed $value,
        protected bool $hit,
        protected ?DateTimeInterface $expiration = null,
    ) {
    }

    final public function get(): mixed
    {
        return $this->isHit() ? $this->value : null;
    }

    /**
     * Returns the expiration timestamp.
     */
    final public function getExpiration(): DateTimeInterface
    {
        return $this->expiration ?? new DateTimeImmutable('now +1 year');
    }

    final public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Returns the raw value, regardless of hit status.
     */
    final public function getRawValue(): mixed
    {
        return $this->value;
    }

    final public function isHit(): bool
    {
        return $this->hit;
    }

    abstract public function expiresAfter(int | DateInterval | null $time): static;

    abstract public function expiresAt(?DateTimeInterface $expiration): static;

    abstract public function set(mixed $value): static;
}
