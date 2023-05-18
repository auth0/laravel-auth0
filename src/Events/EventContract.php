<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * @api
 */
interface EventContract
{
    /**
     * Returns whether an event payload has been overwritten.
     */
    public function wasMutated(): bool;
}
