<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * Raised when a login attempt is made.
 *
 * @api
 */
final class LoginAttempting extends EventAbstract implements LoginAttemptingContract
{
    public function __construct(
        private array $parameters = [],
    ) {
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters): self
    {
        $this->parameters = $parameters;

        return $this;
    }
}
