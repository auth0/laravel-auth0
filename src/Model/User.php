<?php

declare(strict_types=1);

namespace Auth0\Laravel\Model;

abstract class User implements \Illuminate\Contracts\Auth\Authenticatable, \Auth0\Laravel\Contract\Model\User
{
    /**
     * The model's attributes.
     */
    private array $attributes = [];

    /**
     * @inheritdoc
     */
    public function __construct(
        array $attributes = []
    ) {
        $this->fill($attributes);
    }

    /**
     * @inheritdoc
     */
    public function __get(
        string $key
    ) {
        return $this->getAttribute($key);
    }

    /**
     * @inheritdoc
     */
    public function __set(
        string $key,
        $value
    ): void {
        $this->setAttribute($key, $value);
    }

    /**
     * @inheritdoc
     */
    public function fill(
        array $attributes
    ): self {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setAttribute(
        string $key,
        $value
    ): self {
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAttribute(
        string $key,
        $default = null
    ) {
        return $this->attributes[$key] ?? $default;
    }

    /**
     * @inheritdoc
     */
    public function getAuthIdentifier()
    {
        return $this->attributes['sub'] ?? $this->attributes['user_id'] ?? $this->attributes['email'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * @inheritdoc
     */
    public function getAuthPassword(): string
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function getRememberToken(): string
    {
        return '';
    }

    /**
     * @inheritdoc
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function setRememberToken(
        $value
    ): void {
    }

    /**
     * @inheritdoc
     */
    public function getRememberTokenName(): string
    {
        return '';
    }
}
