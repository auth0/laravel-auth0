<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * @internal
 *
 * @api
 */
abstract class LoginAttemptingAbstract extends EventAbstract
{
    /**
     * @param array<string, null|int|string> $parameters Additional API parameters to be sent with the authentication request.
     */
    public function __construct(
        public array $parameters = [],
    ) {
    }

    /**
     * @psalm-suppress LessSpecificImplementedReturnType
     *
     * @return array{parameters: mixed}
     */
    final public function jsonSerialize(): array
    {
        return [
            'parameters' => json_decode(json_encode($this->parameters, JSON_THROW_ON_ERROR), true),
        ];
    }
}
