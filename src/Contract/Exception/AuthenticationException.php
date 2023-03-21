<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Exception;

use Auth0\SDK\Exception\Auth0Exception;

interface AuthenticationException extends Auth0Exception
{
    /**
     * @var string
     */
    public const UNAUTHENTICATED = 'Unauthenticated.';
}
