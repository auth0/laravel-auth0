<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Auth;

interface Guard
{
    /**
     * Create a new authentication guard.
     *
     * @param \Illuminate\Contracts\Auth\UserProvider $provider
     * @param \Illuminate\Http\Request $request
     * @param string $inputKey
     * @param string $storageKey
     * @param bool $hash
     */
    public function __construct(
        \Illuminate\Contracts\Auth\UserProvider $provider,
        \Illuminate\Http\Request $request,
        $inputKey = 'api_token',
        $storageKey = 'api_token',
        $hash = false
    );

    /**
     * Set the current user.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     */
    public function login(
        \Illuminate\Contracts\Auth\Authenticatable $user
    ): self;

    /**
     * Clear the current user.
     */
    public function logout(): self;

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check(): bool;

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest(): bool;

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|string|null
     */
    public function id();

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     */
    public function validate(
        array $credentials = []
    ): bool;

    /**
     * Determine if the guard has a user instance.
     */
    public function hasUser(): bool;

    /**
     * Set the current user.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     */
    public function setUser(
        \Illuminate\Contracts\Auth\Authenticatable $user
    ): self;

    /**
     * Set the current request instance.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function setRequest(
        \Illuminate\Http\Request $request
    ): self;

    /**
     * Get the currently authenticated user.
     */
    public function user(): ?\Illuminate\Contracts\Auth\Authenticatable;

    /**
     * Get the token for the current request.
     */
    public function getTokenForRequest(): ?string;
}
