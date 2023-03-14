<?php

declare(strict_types=1);

namespace Auth0\Laravel\Model;

use Auth0\Laravel\Contract\Model\User as UserContract;
use Illuminate\Contracts\Auth\Authenticatable;

abstract class User implements Authenticatable, UserContract
{
    public function __construct(private array $attributes = [])
    {
        $this->fill($attributes);
    }

    public function __get(string $key): mixed
    {
        return $this->getAttribute($key);
    }

    public function __set(string $key, $value): void
    {
        $this->setAttribute($key, $value);
    }

    final public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    final public function getAttribute(string $key, $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    final public function getAttributes(): mixed
    {
        return $this->attributes;
    }

    final public function getAuthIdentifier(): int | string | null
    {
        return $this->attributes['sub'] ?? $this->attributes['user_id'] ?? $this->attributes['email'] ?? null;
    }

    final public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    final public function getAuthPassword(): string
    {
        return '';
    }

    final public function getRememberToken(): string
    {
        return '';
    }

    final public function getRememberTokenName(): string
    {
        return '';
    }

    final public function jsonSerialize(): mixed
    {
        return $this->attributes;
    }

    final public function setAttribute(string $key, mixed $value): self
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    final public function setRememberToken($value): void
    {
    }
}
