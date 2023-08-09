<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events;

/**
 * @api
 */
interface EventContract
{
    /**
     * A representation of the event context which can be serialized by json_encode().
     *
     * @psalm-suppress LessSpecificImplementedReturnType
     */
    public function jsonSerialize(): mixed;
}
