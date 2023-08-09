<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events\Middleware;

use Auth0\Laravel\Events\EventContract;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

/**
 * @api
 */
interface StatelessMiddlewareRequestContract extends EventContract
{
    /**
     * @return array{request: array<array-key, mixed>, guard: array<array-key, mixed>}
     */
    public function jsonSerialize(): array;
}
