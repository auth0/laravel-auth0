<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * Base event class.
 *
 * @api
 */
abstract class EventAbstract
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
