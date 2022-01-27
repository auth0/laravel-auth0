<?php

declare(strict_types=1);

namespace Auth0\Laravel\Auth;

final class Guard implements \Illuminate\Contracts\Auth\Guard, \Auth0\Laravel\Contract\Auth\Guard
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
     * The name of the query string item from the request containing the API token.
     */
    private string $inputKey;

    /**
     * The name of the token "column" in persistent storage.
     */
    private string $storageKey;

    /**
     * Indicates if the API token is hashed in storage.
     */
    private bool $hash = false;

    /**
     * @inheritdoc
     */
    public function __construct(
        \Illuminate\Contracts\Auth\UserProvider $provider,
        \Illuminate\Http\Request $request,
        $inputKey = 'api_token',
        $storageKey = 'api_token',
        $hash = false
    ) {
        $this->provider = $provider;
        $this->request = $request;
        $this->inputKey = $inputKey;
        $this->storageKey = $storageKey;
        $this->hash = $hash;
    }

    /**
     * @inheritdoc
     */
    public function login(
        \Illuminate\Contracts\Auth\Authenticatable $user
    ): self {
        $this->getInstance()->setUser($user);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function logout(): self
    {
        $this->getInstance()->setUser(null);
        app('auth0')->getSdk()->clear();
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
    public function id()
    {
        $response = null;

        if ($this->user() !== null) {
            $id = $this->user()->getAuthIdentifier();

            if (is_string($id) || is_int($id)) {
                $response = $id;
            }
        }

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function validate(
        array $credentials = []
    ): bool {
        if (! isset($credentials[$this->inputKey])) {
            return false;
        }

        $credentials = [$this->storageKey => $credentials[$this->inputKey]];

        return $this->provider->retrieveByCredentials($credentials) !== null;
    }

    /**
     * @inheritdoc
     */
    public function hasUser(): bool
    {
        return ! is_null($this->getInstance()->getUser());
    }

    /**
     * @inheritdoc
     */
    public function hasScope(
        string $scope
    ): bool {
        $user = $this->getInstance()->getUser();

        if ($user !== null && in_array($scope, $user->getAccessTokenScope(), true)) {
            return true;
        }

        return false;
    }

    /**
     *  @inheritdoc
     */
    public function setUser(
        \Illuminate\Contracts\Auth\Authenticatable $user
    ): self {
        $user = $this->getInstance()->setUser($user);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setRequest(
        \Illuminate\Http\Request $request
    ): self {
        $this->request = $request;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function user(): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        $instance = $this->getInstance();
        $user = null;
        $token = $this->getTokenForRequest();

        if ($token !== null) {
            $user = $this->provider->retrieveByToken([], $token);
        }

        if ($user === null && $token === null) {
            $user = $this->getUserFromSession();
            $user = $this->handleTokenExpiration($user);
        }

        if ($user !== null) {
            $instance->setUser($user);
        }

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function getTokenForRequest(): ?string
    {
        $token = $this->request->query($this->inputKey);

        if ($token === null) {
            $token = $this->request->input($this->inputKey);
        }

        if ($token === null) {
            $token = $this->request->bearerToken();
        }

        if ($token === null) {
            $token = $this->request->getPassword();
        }

        if ($token !== null && is_string($token)) {
            return $token;
        }

        return null;
    }

    /**
     * Return the current request's StateInstance singleton.
     */
    private function getInstance(): \Auth0\Laravel\StateInstance
    {
        return app()->make(\Auth0\Laravel\StateInstance::class);
    }

    /**
     * Get the local user session.
     */
    private function getUserFromSession(): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        $session = app('auth0')->getSdk()->getCredentials();

        if ($session !== null) {
            return $this->provider->retrieveByCredentials((array) $session);
        }

        return null;
    }

    /**
     * Handle instances of access token expiration.
     */
    private function handleTokenExpiration(
        ?\Illuminate\Contracts\Auth\Authenticatable $user
    ): ?\Illuminate\Contracts\Auth\Authenticatable {
        if ($user === null) {
            return null;
        }

        if ($user->getAccessTokenExpired() === false) {
            return $user;
        }

        // Did we scope with 'offline_access' (and does the API allow) for a refresh token?
        if ($user->getRefreshToken() !== null) {
            try {
                app('auth0')->getSdk()->renew();
            } catch (\Auth0\SDK\Exception\StateException $tokenRefreshFailed) {
                // Inform the host application token refresh failed, to enable custom handling behavior
                event(new \Auth0\Laravel\Event\Stateful\TokenRefreshFailed());
            }

            // Retrieve any potentially updated state data
            $refreshed = $this->getUserFromSession();

            // Was refreshed successfully?
            if ($refreshed !== null && $refreshed->getAccessTokenExpired() === false) {
                // Inform the host application to enable custom handling behavior
                $event = new \Auth0\Laravel\Event\Stateful\TokenRefreshSucceeded($refreshed);
                event($event);

                return $event->getUser();
            }
        }

        // We didn't have a refresh token, or the refresh failed. Inform host application.
        $event = new \Auth0\Laravel\Event\Stateful\TokenExpired($user);
        event($event);

        // Did the host application override default expiration handling?
        if ($event->getUser()->getAccessTokenExpired() === false) {
            // Unless the host application expressly opted into not clearing the local user session, do so:
            return $event->getUser();
        }

        if ($event->getClearSession() === true) {
            // Clear the local user session:
            $this->getInstance()->setUser(null);
            app('auth0')->getSdk()->clear();
        }

        return null;
    }
}
