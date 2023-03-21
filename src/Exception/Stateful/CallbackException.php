<?php

declare(strict_types=1);

namespace Auth0\Laravel\Exception\Stateful;

use Auth0\Laravel\Contract\Exception\Stateful\CallbackException as CallbackExceptionContract;
use Auth0\SDK\Exception\Auth0Exception;
use Exception;

/**
 * @codeCoverageIgnore
 */
final class CallbackException extends Exception implements Auth0Exception, CallbackExceptionContract
{
    /**
     * @var string
     */
    public const MSG_API_RESPONSE = '%s: %s';
}
