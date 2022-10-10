<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Http\Controller\Stateful;

interface Callback
{
    /**
     * Process the session for the end user after returning from authenticating with Auth0.
     *
     * @param  \Illuminate\Http\Request  $request  the incoming request instance
     */
    public function __invoke(\Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse;
}
