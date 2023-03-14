<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Http\Controller\Stateful;

use Illuminate\Http\{RedirectResponse, Request};

interface Callback
{
    /**
     * Process the session for the end user after returning from authenticating with Auth0.
     *
     * @param Request $request the incoming request instance
     */
    public function __invoke(Request $request): RedirectResponse;
}
