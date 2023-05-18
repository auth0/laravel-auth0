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
    public function __construct(
        protected array $parameters = [],
    ) {
    }

    final public function getParameters(): array
    {
        return $this->parameters;
    }

    final public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }
}
