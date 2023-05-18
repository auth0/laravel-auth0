<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Controller\Stateful;

use Auth0\Laravel\Http\Controller\ControllerContract;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

interface CallbackContract extends ControllerContract
{
    /**
     * Process the session for the end user after returning from authenticating with Auth0.
     *
     * @param Request $request the incoming request instance
     */
    public function __invoke(Request $request): Response;
}
