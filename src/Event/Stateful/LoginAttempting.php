<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

use Auth0\Laravel\Contract\Event\Stateful\LoginAttempting as LoginAttemptingContract;
use Auth0\Laravel\Event\Auth0Event;

final class LoginAttempting extends Auth0Event implements LoginAttemptingContract
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
