<?php

declare(strict_types=1);

namespace Auth0\Laravel\Cache;

use DateInterval;
use DateTime;
use DateTimeInterface;
use Psr\Cache\CacheItemInterface;

final class LaravelCacheItem implements CacheItemInterface
{
    private ?int $expires = null;

    private string $key;

    private mixed $value;

    private bool $is_hit;

    public function __construct(string $key, mixed $value, bool $is_hit)
    {
        $this->key = $key;
        $this->value = $value;
        $this->is_hit = $is_hit;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function get(): mixed
    {
        return $this->value;
    }

    public function isHit(): bool
    {
        return $this->is_hit;
    }

    public function set(mixed $value): static
    {
        $this->value = $value;
        $this->is_hit = true;
        return $this;
    }

    public function expiresAt(mixed $expiration): static
    {
        if ($expiration instanceof DateTimeInterface) {
            $this->expires = $expiration->getTimestamp();
            return $this;
        }

        $this->expires = $expiration;
        return $this;
    }

    /**
     * @param DateInterval|int|null $time
     */
    public function expiresAfter(mixed $time): static
    {
        if ($time === null) {
            $this->expires = null;
            return $this;
        }

        if ($time instanceof DateInterval) {
            $dateTime = new DateTime();
            $dateTime->add($time);
            $this->expires = $dateTime->getTimestamp();
            return $this;
        }

        $this->expires = time() + $time;
        return $this;
    }

    public function expirationTimestamp(): ?int
    {
        return $this->expires;
    }

    public static function miss(string $key): self
    {
        return new self($key, null, false);
    }
}
