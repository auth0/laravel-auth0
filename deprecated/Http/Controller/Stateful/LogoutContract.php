<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Controller\Stateful;

use Auth0\Laravel\Http\Controller\ControllerContract;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

interface LogoutContract extends ControllerContract
{
    /**
     * Redirect to Auth0's logout endpoint if a session is available.
     * Otherwise, redirect to the "/" route.
     *
     * @param Request $request the incoming request instance
     */
    public function __invoke(Request $request): Response;
}
