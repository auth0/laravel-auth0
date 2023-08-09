<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

use Throwable;

/**
 * @internal
 *
 * @api
 */
abstract class AuthenticationFailedAbstract extends EventAbstract
{
    /**
     * @param Throwable $exception      Exception to be thrown following this event.
     * @param bool      $throwException Whether or not $exception will be thrown
     */
    public function __construct(
        public Throwable &$exception,
        public bool $throwException = true,
    ) {
    }

    /**
     * @psalm-suppress LessSpecificImplementedReturnType
     */
    final public function jsonSerialize(): array
    {
        return [
            'exception' => json_decode(json_encode($this->exception, JSON_THROW_ON_ERROR), true),
            'throwException' => $this->throwException,
        ];
    }
}
