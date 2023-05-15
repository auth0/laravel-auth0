<?php

declare(strict_types=1);

namespace Auth0\Laravel\Exceptions;

/**
 * Exception thrown when an error occurs in the SDK's controllers.
 *
 * @codeCoverageIgnore
 *
 * @api
 */
interface ControllerExceptionContract extends ExceptionContract
{
    /**
     * @var string
     */
    public const ROUTED_USING_INCOMPATIBLE_GUARD = 'Requests to this controller must be routed through a Guard configured with an Auth0 driver.';
}
