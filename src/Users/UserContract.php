<?php

declare(strict_types=1);

namespace Auth0\Laravel\Users;

use Illuminate\Contracts\Auth\Authenticatable;
use JsonSerializable;

interface UserContract extends Authenticatable, JsonSerializable
{
    /**
     * @param array $attributes attributes representing the user data
     */
    public function __construct(array $attributes = []);

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param string $key
     */
    public function __get($key);

    /**
     * Dynamically set attributes on the model.
     *
     * @param mixed  $value
     * @param string $key
     */
    public function __set($key, $value);

    /**
     * Fill the model with an array of attributes.
     *
     * @param array $attributes
     */
    public function fill(array $attributes);

    /**
     * Get an attribute from the model.
     *
     * @param mixed  $default
     * @param string $key
     */
    public function getAttribute($key);

    /**
     * Set a given attribute on the model.
     *
     * @param mixed  $value
     * @param string $key
     */
    public function setAttribute($key, $value);
}