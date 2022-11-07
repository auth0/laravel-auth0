<?php

declare(strict_types=1);

namespace Auth0\Laravel\Auth;

use Auth0\Laravel\Auth0;
use Auth0\Laravel\Contract\Auth\User\Provider;
use Auth0\Laravel\Contract\StateInstance;
use Auth0\Laravel\StateInstance as ConcreteStateInstance;
use Auth0\SDK\Configuration\SdkConfiguration;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Session;
use RuntimeException;

final class Guard implements \Auth0\Laravel\Contract\Auth\Guard, \Illuminate\Contracts\Auth\Guard
{
    /**
     * {@inheritdoc}
     */
    public function login(Authenticatable $user): self
    {
        $this->getState()->setUser($user);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function logout(): self
    {
        // Although user() should never return null in this instance, default to an empty dummy user in such an event to avoid throwing an exception.
        $user = $this->user() ?? new \Auth0\Laravel\Model\Stateful\User([]);

        event(new \Illuminate\Auth\Events\Logout(Guard::class, $user));

        app()->instance(StateInstance::class, null);
        Session::flush();

        app(Auth0::class)->getSdk()->clear();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function check(): bool
    {
        return null !== $this->user();
    }

    /**
     * {@inheritdoc}
     */
    public function guest(): bool
    {
        return ! $this->check();
    }

    /**
     * {@inheritdoc}
     */
    public function user(): ?Authenticatable
    {
        $user = $this->getState()->getUser();

        if (! $user instanceof Authenticatable) {
            $configuration = app(Auth0::class)->getConfiguration();

            $apiOnly = \in_array($configuration->getStrategy(), [SdkConfiguration::STRATEGY_API, SdkConfiguration::STRATEGY_MANAGEMENT_API], true);

            if ($apiOnly) {
                $user = $this->getUserFromToken();
            }

            if (! $apiOnly) {
                $user = $this->getUserFromSession();
            }
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function id()
    {
        $response = null;
        $user = $this->user();

        if (null !== $user) {
            $id = $user->getAuthIdentifier();

            if (\is_string($id) || \is_int($id)) {
                $response = $id;
            }
        }

        return $response;
    }

    /**
     * {@inheritdoc}
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     */
    public function validate(array $credentials = []): bool
    {
        return false;
    }

    /**
     *  {@inheritdoc}
     *
     * @psalm-suppress UnusedVariable
     */
    public function setUser(Authenticatable $user): self
    {
        $user = $this->getState()->
            setUser($user);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasUser(): bool
    {
        return null !== $this->getState()->getUser();
    }

    /**
     * {@inheritdoc}
     */
    public function hasScope(string $scope): bool
    {
        $state = $this->getState();

        return \in_array($scope, $state->getAccessTokenScope() ?? [], true);
    }

    /**
     * Always returns false to keep third-party apps happy.
     */
    public function viaRemember(): bool
    {
        return false;
    }

    /**
     * Get the user context from a provided access token.
     */
    private function getUserFromToken(): ?Authenticatable
    {
        // Retrieve an available bearer token from the request.
        $request = request();

        // @phpstan-ignore-next-line
        if (! $request instanceof \Illuminate\Http\Request) {
            return null;
        }

        $token = $request->bearerToken();

        // If a session is not available, return null.
        if (! \is_string($token)) {
            return null;
        }

        $event = new \Auth0\Laravel\Event\Stateless\TokenVerificationAttempting($token);
        event($event);
        $token = $event->getToken();

        try {
            // Attempt to decode the bearer token.
            $decoded = app(Auth0::class)->getSdk()->decode(
                token: $token,
                tokenType: \Auth0\SDK\Token::TYPE_TOKEN,
            )->toArray();
        } catch (\Auth0\SDK\Exception\InvalidTokenException $invalidToken) {
            event(new \Auth0\Laravel\Event\Stateless\TokenVerificationFailed($token, $invalidToken));

            // Invalid bearer token.
            return null;
        }

        // Query the UserProvider to retrieve tue user for the token.
        $provider = $this->getProvider();

        /**
         * @var Provider $provider
         * @var array{scope: string|null, exp: int|null} $decoded
         */
        $user = $provider->
            getRepository()->
            fromAccessToken($decoded);

        // Was a user retrieved successfully?
        if (null !== $user) {
            if (! $user instanceof \Auth0\Laravel\Contract\Model\Stateless\User) {
                exit('User model returned fromAccessToken must implement \Auth0\Laravel\Contract\Model\Stateless\User.');
            }

            event(new \Auth0\Laravel\Event\Stateless\TokenVerificationSucceeded($token, $decoded));

            $this->getState()->
                clear()->
                setDecoded($decoded)->
                setAccessToken($token)->
                setAccessTokenScope(explode(' ', $decoded['scope'] ?? ''))->
                setAccessTokenExpiration($decoded['exp'] ?? null);
        }

        return $user;
    }

    /**
     * Get the user context from an Auth0-PHP SDK session..
     */
    private function getUserFromSession(): ?Authenticatable
    {
        // Retrieve an available session from the Auth0-PHP SDK.
        $session = app(Auth0::class)->getSdk()->getCredentials();

        // If a session is not available, return null.
        if (null === $session) {
            return null;
        }

        // Query the UserProvider to retrieve tue user for the token.
        $provider = $this->getProvider();

        /**
         * @var Provider $provider
         */

        // Query the UserProvider to retrieve tue user for the session.
        $user = $provider->
            getRepository()->
            fromSession($session->user); // @phpstan-ignore-line

        // Was a user retrieved successfully?
        if (null !== $user) {
            if (! $user instanceof \Auth0\Laravel\Contract\Model\Stateful\User) {
                exit('User model returned fromSession must implement \Auth0\Laravel\Contract\Model\Stateful\User.');
            }

            $this->getState()->
                clear()->
                setDecoded($session->user)-> // @phpstan-ignore-line
                setIdToken($session->idToken)-> // @phpstan-ignore-line
                setAccessToken($session->accessToken)-> // @phpstan-ignore-line
                setAccessTokenScope($session->accessTokenScope)-> // @phpstan-ignore-line
                setAccessTokenExpiration($session->accessTokenExpiration)-> // @phpstan-ignore-line
                setRefreshToken($session->refreshToken); /** @phpstan-ignore-line */
            $user = $this->handleSessionExpiration($user);
        }

        return $user;
    }

    /**
     * Handle instances of session token expiration.
     */
    private function handleSessionExpiration(
        ?Authenticatable $user,
    ): ?Authenticatable {
        $state = $this->getState();

        // Unless our token expired, we have nothing to do here.
        if (true !== $state->getAccessTokenExpired()) {
            return $user;
        }

        // Do we have a refresh token?
        if (null !== $state->getRefreshToken()) {
            try {
                // Try to renew our token.
                app(Auth0::class)->getSdk()->renew();
            } catch (\Auth0\SDK\Exception\StateException $tokenRefreshFailed) {
                // Renew failed. Inform application.
                event(new \Auth0\Laravel\Event\Stateful\TokenRefreshFailed());
            }

            // Retrieve updated state data
            $refreshed = app(Auth0::class)->getSdk()->getCredentials();

            // @phpstan-ignore-next-line
            if (null !== $refreshed && false === $refreshed->accessTokenExpired) {
                event(new \Auth0\Laravel\Event\Stateful\TokenRefreshSucceeded());

                return $user;
            }
        }

        // We didn't have a refresh token, or the refresh failed.
        // Clear session.
        $state->clear();
        app(Auth0::class)->getSdk()->clear();

        // Inform host application.
        event(new \Auth0\Laravel\Event\Stateful\TokenExpired());

        return null;
    }

    /**
     * Return the current request's StateInstance singleton.
     */
    private function getState(): StateInstance
    {
        return app(ConcreteStateInstance::class);
    }

    /**
     * Return the current request's StateInstance singleton.
     */
    private function getProvider(): UserProvider
    {
        static $provider = null;

        if (null === $provider) {
            /**
             * @var string|null $configured
             */
            $configured = config('auth.guards.auth0.provider') ?? \Auth0\Laravel\Auth\User\Provider::class;
            $provider = app('auth')->createUserProvider($configured);

            if (! $provider instanceof UserProvider) {
                throw new RuntimeException('Auth0: Unable to invoke UserProvider from application configuration.');
            }
        }

        return $provider;
    }
}
