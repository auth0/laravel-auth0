<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Http\Controller\Stateful;

use Illuminate\Http\{RedirectResponse, Request};

interface Login
{
    /**
     * Redirect to the configured Auth0 Universal Login Page if a session is not available.
     * Otherwise, redirect to the "/" route.
     *
     * @param Request $request the incoming request instance
     */
    public function __invoke(Request $request): RedirectResponse;
}
