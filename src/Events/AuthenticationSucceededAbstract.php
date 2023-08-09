<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * @internal
 *
 * @api
 */
abstract class AuthenticationSucceededAbstract extends EventAbstract
{
    /**
     * @param Authenticatable $user The user that successfully authenticated.
     */
    public function __construct(
        public Authenticatable &$user,
    ) {
    }

    /**
     * @psalm-suppress LessSpecificImplementedReturnType
     *
     * @return array{user: mixed}
     */
    final public function jsonSerialize(): array
    {
        return [
            'user' => json_decode(json_encode($this->user, JSON_THROW_ON_ERROR), true),
        ];
    }
}
