<?php

declare(strict_types=1);

namespace Auth0\Laravel\Auth;

final class Guard implements \Auth0\Laravel\Contract\Auth\Guard, \Illuminate\Contracts\Auth\Guard
{
    /**
     * The user provider implementation.
     */
    private \Illuminate\Contracts\Auth\UserProvider $provider;

    /**
     * The request instance.
     */
    private \Illuminate\Http\Request $request;

    /**
     * @inheritdoc
     */
    public function __construct(
        \Illuminate\Contracts\Auth\UserProvider $provider,
        \Illuminate\Http\Request $request
    ) {
        $this->provider = $provider;
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    public function login(\Illuminate\Contracts\Auth\Authenticatable $user): self
    {
        $this->getState()
            ->setUser($user);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function logout(): self
    {
        $this->getState()
            ->setUser(null);
        app(\Auth0\Laravel\Auth0::class)->getSdk()->clear();
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function check(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @inheritdoc
     */
    public function guest(): bool
    {
        return ! $this->check();
    }

    /**
     * @inheritdoc
     */
    public function user(): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        return $this->getState()
            ->getUser() ?? $this->getUserFromToken() ?? $this->getUserFromSession() ?? null;
    }

    /**
     * @inheritdoc
     */
    public function id()
    {
        $response = null;

        if ($this->user() !== null) {
            $id = $this->user()
                ->getAuthIdentifier();

            if (is_string($id) || is_int($id)) {
                $response = $id;
            }
        }

        return $response;
    }

    /**
     * @inheritdoc
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function validate(array $credentials = []): bool
    {
        return false;
    }

    /**
     *  @inheritdoc
     */
    public function setUser(\Illuminate\Contracts\Auth\Authenticatable $user): self
    {
        $user = $this->getState()
            ->setUser($user);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function hasUser(): bool
    {
        return $this->getState()->getUser() !== null;
    }

    /**
     * @inheritdoc
     */
    public function hasScope(string $scope): bool
    {
        $state = $this->getState();
        return in_array($scope, $state->getAccessTokenScope() ?? [], true);
    }

    /**
     * Always returns false to keep third-party apps happy
     */
    public function viaRemember(): bool
    {
        return false;
    }

    /**
     * Get the user context from a provided access token.
     */
    private function getUserFromToken(): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        // Retrieve an available bearer token from the request.
        $token = $this->request->bearerToken();

        // If a session is not available, return null.
        if ($token === null) {
            return null;
        }

        try {
            // Attempt to decode the bearer token.
            $decoded = app(\Auth0\Laravel\Auth0::class)->getSdk()->decode(
                $token,
                null,
                null,
                null,
                null,
                null,
                null,
                \Auth0\SDK\Token::TYPE_TOKEN
            )->toArray();
        } catch (\Auth0\SDK\Exception\InvalidTokenException $invalidToken) {
            // Invalid bearer token.
            return null;
        }

        // Query the UserProvider to retrieve tue user for the token.
        $user = $this->getProvider()
            ->getRepository()
            ->fromAccessToken($decoded);

        // Was a user retrieved successfully?
        if ($user !== null) {
            if (! $user instanceof \Illuminate\Contracts\Auth\Authenticatable) {
                die('User model returned fromAccessToken must implement \Illuminate\Contracts\Auth\Authenticatable.');
            }

            if (! $user instanceof \Auth0\Laravel\Contract\Model\Stateless\User) {
                die('User model returned fromAccessToken must implement \Auth0\Laravel\Contract\Model\Stateless\User.');
            }

            $this->getState()
                ->clear()
                ->setDecoded($decoded)
                ->setAccessToken($token)
                ->setAccessTokenScope(explode(' ', $decoded['scope'] ?? ''))
                ->setAccessTokenExpiration($decoded['exp'] ?? null);
        }

        return $user;
    }

    /**
     * Get the user context from an Auth0-PHP SDK session..
     */
    private function getUserFromSession(): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        // Retrieve an available session from the Auth0-PHP SDK.
        $session = app(\Auth0\Laravel\Auth0::class)->getSdk()->getCredentials();

        // If a session is not available, return null.
        if ($session === null) {
            return null;
        }

        // Query the UserProvider to retrieve tue user for the session.
        $user = $this->getProvider()
            ->getRepository()
            ->fromSession($session->user);

        // Was a user retrieved successfully?
        if ($user !== null) {
            if (! $user instanceof \Illuminate\Contracts\Auth\Authenticatable) {
                die('User model returned fromSession must implement \Illuminate\Contracts\Auth\Authenticatable.');
            }

            if (! $user instanceof \Auth0\Laravel\Contract\Model\Stateful\User) {
                die('User model returned fromSession must implement \Auth0\Laravel\Contract\Model\Stateful\User.');
            }

            $this->getState()
                ->clear()
                ->setDecoded($session->user)
                ->setIdToken($session->idToken)
                ->setAccessToken($session->accessToken)
                ->setAccessTokenScope($session->accessTokenScope)
                ->setAccessTokenExpiration($session->accessTokenExpiration)
                ->setRefreshToken($session->refreshToken);

            $user = $this->handleSessionExpiration($user);
        }

        return $user;
    }

    /**
     * Handle instances of session token expiration.
     */
    private function handleSessionExpiration(
        ?\Illuminate\Contracts\Auth\Authenticatable $user
    ): ?\Illuminate\Contracts\Auth\Authenticatable {
        $state = $this->getState();

        // Unless our token expired, we have nothing to do here.
        if ($state->getAccessTokenExpired() !== true) {
            return $user;
        }

        // Do we have a refresh token?
        if ($state->getRefreshToken() !== null) {
            try {
                // Try to renew our token.
                app(\Auth0\Laravel\Auth0::class)->getSdk()->renew();
            } catch (\Auth0\SDK\Exception\StateException $tokenRefreshFailed) {
                // Renew failed. Inform application.
                event(new \Auth0\Laravel\Event\Stateful\TokenRefreshFailed());
            }

            // Retrieve updated state data
            $refreshed = app(\Auth0\Laravel\Auth0::class)->getSdk()->getCredentials();

            if ($refreshed !== null && $refreshed->accessTokenExpired === false) {
                event(new \Auth0\Laravel\Event\Stateful\TokenRefreshSucceeded());
                return $user;
            }
        }

        // We didn't have a refresh token, or the refresh failed.
        // Clear session.
        $state->clear();
        app(\Auth0\Laravel\Auth0::class)->getSdk()->clear();

        // Inform host application.
        event(new \Auth0\Laravel\Event\Stateful\TokenExpired());

        return null;
    }

    /**
     * Return the current request's StateInstance singleton.
     */
    private function getState(): \Auth0\Laravel\StateInstance
    {
        return app()->make(\Auth0\Laravel\StateInstance::class);
    }

    /**
     * Return the current request's StateInstance singleton.
     */
    private function getProvider(): \Illuminate\Contracts\Auth\UserProvider
    {
        return $this->provider;
    }
}
