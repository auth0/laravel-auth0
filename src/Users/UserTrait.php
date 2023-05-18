<?php

declare(strict_types=1);

namespace Auth0\Laravel\Users;

/**
 * @api
 */
trait UserTrait
{
    final public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    final public function setAttribute(string $key, mixed $value): self
    {
        $this->attributes[$key] = $value;

        return $this;
    }
}
