<?php

declare(strict_types=1);

namespace Auth0\Laravel\Cache;

use Psr\Cache\CacheItemInterface;

final class LaravelCacheItem implements CacheItemInterface
{
    public function __construct(
        private string $key,
        private mixed $value,
        private bool $hit,
        private ?\DateTimeInterface $expiration = null,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function get(): mixed
    {
        return $this->isHit() ? $this->value : null;
    }

    /**
     * {@inheritdoc}
     */
    public function set($value = null): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isHit(): bool
    {
        return $this->hit;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAt(?\DateTimeInterface $expiration): static
    {
        $this->expiration = $expiration ?? new \DateTimeImmutable('now +1 year');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAfter(int|\DateInterval|null $time): static
    {
        $this->expiration = match (true) {
            null === $time                 => new \DateTimeImmutable('now +1 year'),
            \is_int($time)                 => new \DateTimeImmutable('now +' . $time . ' seconds'),
            $time instanceof \DateInterval => (new \DateTimeImmutable())->add($time), /* @phpstan-ignore-line */
        };

        return $this;
    }

    /**
     * Returns the expiration timestamp.
     */
    public function getExpiration(): \DateTimeInterface
    {
        return $this->expiration ?? new \DateTime('now +1 year');
    }

    /**
     * Returns the raw value, regardless of hit status.
     */
    public function getRawValue(): mixed
    {
        return $this->value;
    }

    /**
     * Return a LaravelCacheItem instance flagged as missed.
     */
    public static function miss(string $key): self
    {
        return new self(
            key: $key,
            value: null,
            hit: false,
        );
    }
}
