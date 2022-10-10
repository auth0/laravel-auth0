<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Auth;

interface Guard
{
    /**
     * Create a new authentication guard.
     */
    public function __construct(
        \Illuminate\Contracts\Auth\UserProvider $provider,
        \Illuminate\Http\Request $request
    );

    /**
     * Set the current user.
     */
    public function login(\Illuminate\Contracts\Auth\Authenticatable $user): self;

    /**
     * Clear the current user.
     */
    public function logout(): self;

    /**
     * Determine if the guard has a user instance.
     */
    public function hasUser(): bool;

    /**
     * Get the currently authenticated user.
     */
    public function user(): ?\Illuminate\Contracts\Auth\Authenticatable;
}
