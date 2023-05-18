<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

use Auth0\Laravel\Events\EventAbstract;
use Auth0\Laravel\Events\LoginAttemptingContract;

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
