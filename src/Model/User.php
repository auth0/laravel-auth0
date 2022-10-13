<?php

declare(strict_types=1);

namespace Auth0\Laravel\Model;

abstract class User implements \Illuminate\Contracts\Auth\Authenticatable, \Auth0\Laravel\Contract\Model\User
{
    /**
     * {@inheritdoc}
     */
    public function __construct(private array $attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function __get(string $key)
    {
        return $this->getAttribute($key);
    }

    /**
     * {@inheritdoc}
     */
    public function __set(string $key, $value): void
    {
        $this->setAttribute($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    final public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function setAttribute(string $key, $value): self
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    final public function getAttribute(string $key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }

    /**
     * {@inheritdoc}
     */
    final public function getAuthIdentifier()
    {
        return $this->attributes['sub'] ?? $this->attributes['user_id'] ?? $this->attributes['email'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    final public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * {@inheritdoc}
     */
    final public function getAuthPassword(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    final public function getRememberToken(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    final public function setRememberToken($value): void
    {
    }

    /**
     * {@inheritdoc}
     */
    final public function getRememberTokenName(): string
    {
        return '';
    }
}
