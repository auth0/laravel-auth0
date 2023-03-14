<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Controller;

use Auth0\Laravel\Auth0 as Service;
use Auth0\Laravel\Facade\Auth0;
use Auth0\SDK\Contract\Auth0Interface as SDK;
use Illuminate\Routing\Controller;

/**
 * @codeCoverageIgnore
 */
abstract class ControllerAbstract extends Controller
{
    final public function getSdk(): SDK
    {
        $service = app('auth0');

        // @phpstan-ignore-next-line
        if ($service instanceof Service) {
            return $service->getSdk();
        }

        // @phpstan-ignore-next-line
        return Auth0::getSdk();
    }
}
