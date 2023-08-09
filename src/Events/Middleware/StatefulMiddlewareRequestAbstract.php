<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events\Middleware;

use Auth0\Laravel\Events\EventAbstract;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

/**
 * @internal
 *
 * @api
 */
abstract class StatefulMiddlewareRequestAbstract extends EventAbstract
{
    /**
     * @param Request $request Incoming request.
     * @param Guard   $guard   The guard being used to authenticate the request.
     */
    public function __construct(
        public Request &$request,
        public Guard &$guard,
    ) {
    }

    /**
     * @psalm-suppress LessSpecificImplementedReturnType
     *
     * @return array{request: mixed, guard: mixed}
     */
    final public function jsonSerialize(): array
    {
        return [
            'request' => json_decode(json_encode($this->request, JSON_THROW_ON_ERROR), true),
            'guard' => json_decode(json_encode($this->guard, JSON_THROW_ON_ERROR), true),
        ];
    }
}
