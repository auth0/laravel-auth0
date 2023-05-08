<?php

declare(strict_types=1);

namespace Auth0\Laravel\Exception;

use Auth0\SDK\Exception\Auth0Exception;

interface ControllerExceptionContract extends Auth0Exception
{
    /**
     * @var string
     */
    public const ROUTED_USING_INCOMPATIBLE_GUARD = 'Requests to this controller must be routed through a Guard configured with an Auth0 driver.';
}
