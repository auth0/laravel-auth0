<?php

declare(strict_types=1);

namespace Auth0\Laravel\Users;

/**
 * @api
 */
abstract class UserAbstract
{
    public function __construct(
        protected array $attributes = [],
    ) {
        $this->fill($attributes);
    }

    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    final public function getAttribute($key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }

    final public function getAttributes()
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

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     *
     * @param mixed $value
     */
    final public function setRememberToken(mixed $value): void
    {
    }

    abstract public function fill(array $attributes): self;

    abstract public function setAttribute($key, $value);
}
