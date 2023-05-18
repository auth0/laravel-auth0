<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Http\Middleware\Stateless;

use Auth0\Laravel\Http\Middleware\Stateless\AuthorizeContract;

/**
 * @codeCoverageIgnore
 * @deprecated 7.8.0 Use Auth0\Laravel\Http\Middleware\Stateful\AuthenticateContract instead.
 * @api
 */
interface Authorize extends AuthorizeContract
{
}
