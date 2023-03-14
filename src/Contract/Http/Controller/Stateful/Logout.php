<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Http\Controller\Stateful;

use Illuminate\Http\{RedirectResponse, Request};

interface Logout
{
    /**
     * Redirect to Auth0's logout endpoint if a session is available.
     * Otherwise, redirect to the "/" route.
     *
     * @param Request $request the incoming request instance
     */
    public function __invoke(Request $request): RedirectResponse;
}
