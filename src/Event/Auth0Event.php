<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event;

abstract class Auth0Event
{
    /**
     * Tracks whether an event payload has been overwritten.
     */
    protected bool $mutated = false;

    /**
     * Returns whether an event payload has been overwritten.
     */
    public function wasMutated(): bool
    {
        return $this->mutated;
    }
}
