<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * @api
 */
interface Auth0EventContract extends EventContract
{
    /**
     * Returns whether an event payload has been overwritten.
     */
    public function wasMutated(): bool;
}
