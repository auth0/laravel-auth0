<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

interface Guard
{
    /**
     * Set the current user.
     */
    public function login(Authenticatable $user): self;

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
    public function user(): ?Authenticatable;

    /**
     * Set the currently authenticated user.
     */
    public function setUser(Authenticatable $user): self;

    /**
     * Determine if an authenticated user is available.
     */
    public function check(): bool;

    /**
     * Returns true if the given user has the specified scope.
     */
    public function hasScope(string $scope): bool;
}
