<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Model;

interface User
{
    /**
     * \Auth0\Laravel\Model\User constructor.
     *
     * @param array $attributes Attributes representing the user data.
     */
    public function __construct(
        array $attributes = []
    );

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get(
        string $key
    );

    /**
     * Dynamically set attributes on the model.
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set(
        string $key,
        $value
    ): void;

    /**
     * Fill the model with an array of attributes.
     *
     * @param array $attributes
     */
    public function fill(
        array $attributes
    ): self;

    /**
     * Set a given attribute on the model.
     *
     * @param string $key
     * @param mixed $value
     */
    public function setAttribute(
        string $key,
        $value
    ): self;

    /**
     * Get an attribute from the model.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getAttribute(
        string $key,
        $default = null
    );
}
