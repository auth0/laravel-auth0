<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Http\Controller\Stateful;

interface Logout
{
    /**
     * Redirect to Auth0's logout endpoint if a session is available.
     * Otherwise, redirect to the "/" route.
     *
     * @param  \Illuminate\Http\Request  $request  the incoming request instance
     */
    public function __invoke(\Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse;
}
