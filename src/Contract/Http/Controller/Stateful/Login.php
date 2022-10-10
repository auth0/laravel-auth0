<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Http\Controller\Stateful;

interface Login
{
    /**
     * Redirect to the configured Auth0 Universal Login Page if a session is not available.
     * Otherwise, redirect to the "/" route.
     *
     * @param  \Illuminate\Http\Request  $request  the incoming request instance
     */
    public function __invoke(\Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse;
}
