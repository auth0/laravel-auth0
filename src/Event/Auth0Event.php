<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event;

/**
 * @codeCoverageIgnore
 */
abstract class Auth0Event implements \Auth0\Laravel\Contract\Event\Auth0Event
{
    /**
     * Tracks whether an event payload has been overwritten.
     */
    protected bool $mutated = false;

    /**
     * Returns whether an event payload has been overwritten.
     */
    final public function wasMutated(): bool
    {
        return $this->mutated;
    }
}
