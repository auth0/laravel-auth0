<?php

declare(strict_types=1);

namespace Auth0\Laravel\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

interface LogoutControllerContract extends ControllerContract
{
    /**
     * Redirect to Auth0's logout endpoint if a session is available.
     * Otherwise, redirect to the "/" route.
     *
     * @param Request $request the incoming request instance
     */
    public function __invoke(Request $request): Response;
}
