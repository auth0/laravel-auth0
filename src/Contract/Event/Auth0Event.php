<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Event;

interface Auth0Event
{
    /**
     * Returns whether an event payload has been overwritten.
     */
    public function wasMutated(): bool;
}
