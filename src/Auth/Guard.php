<?php

declare(strict_types=1);

namespace Auth0\Laravel\Auth;

use Auth0\Laravel\Auth0;
use Auth0\Laravel\Contract\Auth\Guard as GuardContract;
use Auth0\Laravel\Contract\Entities\Credential;
use Auth0\Laravel\Contract\Exception\GuardException as GuardExceptionContract;
use Auth0\Laravel\Entities\Credential as CredentialConcrete;
use Auth0\Laravel\Event\Stateful\{TokenRefreshFailed, TokenRefreshSucceeded};
use Auth0\Laravel\Event\Stateless\{TokenVerificationAttempting, TokenVerificationFailed, TokenVerificationSucceeded};
use Auth0\Laravel\Exception\{AuthenticationException, GuardException};
use Auth0\Laravel\Model\Stateful\User as StatefulUser;
use Auth0\SDK\Contract\Auth0Interface;
use Auth0\SDK\Exception\InvalidTokenException;
use Auth0\SDK\Token;
use Exception;
use Illuminate\Auth\Events\{Login, Logout};
use Illuminate\Contracts\Auth\{Authenticatable, UserProvider};
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Session\Session;
use Psr\Container\{ContainerExceptionInterface, NotFoundExceptionInterface};

use function in_array;
use function is_array;
use function is_int;
use function is_object;
use function is_string;

final class Guard implements GuardContract
{
    /**
     * @var int
     */
    public const SOURCE_IMPERSONATE = 0; // Manually set, presumably through a test case's impersonate() method.

    /**
     * @var int
     */
    public const SOURCE_SESSION     = 2; // Assigned from a session.

    /**
     * @var int
     */
    public const SOURCE_TOKEN       = 1; // Assigned from a decoded token.
    private ?Credential $credential = null;
    private ?int $credentialSource  = null;
    private bool $impersonating     = false;
    private ?UserProvider $provider = null;
    private ?string $pushHash       = null;

    public function __construct(
        private string $name = '',
        private ?array $config = null,
    ) {
    }

    /**
     * Get a credential candidate from an Auth0-PHP SDK session.
     *
     * @return null|Credential Credential when a valid token is found, null otherwise.
     */
    private function findSession(): ?Credential
    {
        $this->getSession();
        $session = $this->pullState();
        $user    = $session?->getUser();

        if (null !== $session && $user instanceof Authenticatable) {
            $user = $this->getProvider()->retrieveByCredentials($this->normalizeUserArray($user));

            if ($user instanceof Authenticatable) {
                $credential = CredentialConcrete::create(
                    user: $user,
                    idToken: $session->getIdToken(),
                    accessToken: $session->getAccessToken(),
                    accessTokenScope: $session->getAccessTokenScope(),
                    accessTokenExpiration: $session->getAccessTokenExpiration(),
                    refreshToken: $session->getRefreshToken(),
                );

                return $this->refreshSession($credential);
            }
        }

        return null;
    }

    /**
     * Get a credential candidate from a provided access token.
     *
     * @return null|Credential Credential object if a valid token is found, null otherwise.
     */
    private function findToken(): ?Credential
    {
        $token = trim(app('request')->bearerToken() ?? '');

        if ('' === $token) {
            return null;
        }

        $user = $this->getProvider()->retrieveByToken('token', $token);

        if ($user instanceof Authenticatable) {
            $data = $this->normalizeUserArray($user);

            if ([] !== $data) {
                $scope = isset($data['scope']) && is_string($data['scope']) ? explode(' ', $data['scope']) : [];
                $exp   = isset($data['exp']) && is_numeric($data['exp']) ? (int) $data['exp'] : null;

                return CredentialConcrete::create(
                    user: $user,
                    accessToken: $token,
                    accessTokenScope: $scope,
                    accessTokenExpiration: $exp,
                );
            }
        }

        return null;
    }

    /**
     * Get the Auth0 PHP SDK instance.
     *
     * @throws BindingResolutionException  If the Auth0 class cannot be resolved.
     * @throws NotFoundExceptionInterface  If the Auth0 service cannot be found.
     * @throws ContainerExceptionInterface If the Auth0 service cannot be resolved.
     *
     * @return Auth0Interface Auth0 PHP SDK instance.
     */
    private function getSdk(): Auth0Interface
    {
        return $this->getService()->getSdk();
    }

    /**
     * Get the Auth0 service instance.
     *
     * @throws BindingResolutionException If the Auth0 class cannot be resolved.
     *
     * @return Auth0 Auth0 service.
     */
    private function getService(): Auth0
    {
        return app('auth0');
    }

    /**
     * Normalize a user model object for easier storage or comparison.
     *
     * @param Authenticatable $user User model object.
     *
     * @throws Exception If the user model object cannot be normalized.
     *
     * @return array<array|int|string> Normalized user model object.
     *
     * @psalm-suppress TypeDoesNotContainType
     *
     * @codeCoverageIgnore
     */
    private function normalizeUserArray(
        Authenticatable $user,
    ): array {
        $implements = class_implements($user);
        $fail       = false;

        // @phpstan-ignore-next-line
        if (in_array('JsonSerializable', $implements, true) && method_exists($user, 'jsonSerialize')) {
            /** @phpstan-ignore-next-line */
            $user = (array) $user->jsonSerialize();
        // @phpstan-ignore-next-line
        } elseif (in_array('Illuminate\Contracts\Support\Arrayable', $implements, true) && method_exists($user, 'toArray')) {
            /** @phpstan-ignore-next-line */
            $user = (array) $user->toArray();
        // @phpstan-ignore-next-line
        } elseif (in_array('Illuminate\Contracts\Support\Jsonable', $implements, true) && method_exists($user, 'toJson')) {
            /** @phpstan-ignore-next-line */
            $user = (array) $user->toJson();
        // @phpstan-ignore-next-line
        } elseif (method_exists($user, 'attributesToArray')) {
            /** @phpstan-ignore-next-line */
            $user = (array) $user->attributesToArray();
        } else {
            $fail = true;
        }
        // @phpstan-ignore-end

        if ($fail) {
            throw new GuardException(GuardExceptionContract::USER_MODEL_NORMALIZATION_FAILURE);
        }

        try {
            // @phpstan-ignore-next-line
            return json_decode(json_encode($user, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new GuardException(GuardExceptionContract::USER_MODEL_NORMALIZATION_FAILURE);
        }
    }

    private function pullState(): ?Credential
    {
        $sdk = $this->getSdk();
        $sdk->refreshState();

        $credentials = $sdk->getCredentials();

        /** @var mixed $credentials */
        if (is_object($credentials) && property_exists($credentials, 'user') && property_exists($credentials, 'idToken') && property_exists($credentials, 'accessToken') && property_exists($credentials, 'accessTokenScope') && property_exists($credentials, 'accessTokenExpiration') && property_exists($credentials, 'refreshToken')) {
            return CredentialConcrete::create(
                user: new StatefulUser($credentials->user),
                idToken: $credentials->idToken,
                accessToken: $credentials->accessToken,
                accessTokenScope: $credentials->accessTokenScope,
                accessTokenExpiration: $credentials->accessTokenExpiration,
                refreshToken: $credentials->refreshToken,
            );
        }

        return null;
    }

    private function pushState(
        ?Credential $credential = null,
    ): self {
        if (self::SOURCE_SESSION !== $this->getCredentialSource()) {
            return $this;
        }

        $sdk = $this->getSdk();
        $credential ??= $this->getCredential();

        if (null === $credential) {
            $sdk->clear(true);

            return $this;
        }

        $pushHash = json_encode($credential);

        // @codeCoverageIgnoreStart
        if (! is_string($pushHash)) {
            $pushHash = '';
        }
        // @codeCoverageIgnoreEnd

        $pushHash = md5($pushHash);

        if ($pushHash === $this->pushHash) {
            return $this;
        }

        $user                  = $credential->getUser();
        $idToken               = $credential->getIdToken();
        $accessToken           = $credential->getAccessToken();
        $accessTokenScope      = $credential->getAccessTokenScope();
        $accessTokenExpiration = $credential->getAccessTokenExpiration();
        $refreshToken          = $credential->getRefreshToken();

        if ($user instanceof Authenticatable) {
            $sdk->setUser($this->normalizeUserArray($user));
        }

        if (null !== $idToken) {
            $sdk->setIdToken($idToken);
        }

        if (null !== $accessToken) {
            $sdk->setAccessToken($accessToken);
        }

        if (null !== $accessTokenScope) {
            $sdk->setAccessTokenScope($accessTokenScope);
        }

        if (null !== $accessTokenExpiration) {
            $sdk->setAccessTokenExpiration($accessTokenExpiration);
        }

        if (null !== $refreshToken) {
            $sdk->setRefreshToken($refreshToken);
        }

        $this->pushHash = $pushHash;

        return $this;
    }

    private function refreshSession(
        ?Credential $credential,
    ): ?Credential {
        if (null === $credential || true !== $credential->getAccessTokenExpired()) {
            return $credential;
        }

        if (null === $credential->getRefreshToken()) {
            return null;
        }

        try {
            $this->getSdk()->renew();
            $session = $this->pullState();
        } catch (\Throwable) {
            event(new TokenRefreshFailed());
            $session = null;
        }

        if (null !== $session) {
            event(new TokenRefreshSucceeded());

            $user = $this->getProvider()->retrieveByCredentials((array) $session->getUser());

            if ($user instanceof Authenticatable) {
                return CredentialConcrete::create(
                    user: $user,
                    idToken: $session->getIdToken(),
                    accessToken: $session->getAccessToken(),
                    accessTokenScope: $session->getAccessTokenScope(),
                    accessTokenExpiration: $session->getAccessTokenExpiration(),
                    refreshToken: $session->getRefreshToken(),
                );
            }
        }

        $this->setCredential(null, null);
        $this->pushState();

        return null;
    }

    public function authenticate(): Authenticatable
    {
        if (null !== ($user = $this->user())) {
            return $user;
        }

        throw new AuthenticationException(AuthenticationException::UNAUTHENTICATED);
    }

    public function check(): bool
    {
        return $this->hasUser();
    }

    public function find(
        int $source,
    ): ?Credential {
        if ($this->impersonating) {
            return $this->getCredential();
        }

        if (self::SOURCE_TOKEN === $source) {
            $candidate = $this->findToken();

            if (null !== $candidate) {
                return $candidate;
            }
        }

        if (self::SOURCE_SESSION === $source) {
            $candidate = $this->findSession();

            if (null !== $candidate) {
                return $candidate;
            }
        }

        return null;
    }

    public function forgetUser(): self
    {
        $this->setCredential();

        return $this;
    }

    public function getCredential(): ?Credential
    {
        if (! $this->impersonating && self::SOURCE_SESSION === $this->getCredentialSource() && null !== $this->credential) {
            $updated = $this->findSession();
            $source  = null !== $updated ? self::SOURCE_SESSION : null;
            $this->setCredential($updated, $source);
            $this->pushState($updated);
        }

        return $this->credential;
    }

    public function getCredentialSource(): ?int
    {
        return $this->credentialSource;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getProvider(): UserProvider
    {
        if ($this->provider instanceof UserProvider) {
            return $this->provider;
        }

        $providerName = $this->config['provider'] ?? '';

        if (! is_string($providerName) || '' === $providerName) {
            // @codeCoverageIgnoreStart
            throw new GuardException(GuardExceptionContract::USER_PROVIDER_UNCONFIGURED);
            // @codeCoverageIgnoreEnd
        }

        $providerName = trim($providerName);
        $provider     = app('auth')->createUserProvider($providerName);

        if ($provider instanceof UserProvider) {
            $this->provider = $provider;

            return $provider;
        }

        // @codeCoverageIgnoreStart
        throw new GuardException(sprintf(GuardExceptionContract::USER_PROVIDER_UNAVAILABLE, $providerName));
        // @codeCoverageIgnoreEnd
    }

    public function getSession(): Session
    {
        $store   = app('session.store');
        $request = app('request');

        if (! $request->hasSession(true)) {
            $request->setLaravelSession($store);
        }

        if (! $store->isStarted()) {
            $store->start();
        }

        return $store;
    }

    public function guest(): bool
    {
        return ! $this->check();
    }

    public function hasScope(
        string $scope,
        Credential $credential,
    ): bool {
        if ('*' === $scope) {
            return true;
        }

        $available = $credential->getAccessTokenScope();

        if (is_array($available) && [] !== $available) {
            return in_array($scope, $available, true);
        }

        return false;
    }

    public function hasUser(): bool
    {
        return null !== $this->getCredential()?->getUser();
    }

    public function id(): int | string | null
    {
        $user = $this->user()?->getAuthIdentifier();

        if (is_string($user) || is_int($user)) {
            return $user;
        }

        return null;
    }

    public function login(
        ?Credential $credential,
        ?int $source = null,
    ): self {
        $this->setCredential($credential, $source);
        $this->pushState($credential);
        $user = $credential?->getUser();

        if (null !== $credential && $user instanceof Authenticatable) {
            event(new Login(self::class, $user, true));
        }

        return $this;
    }

    public function logout(): self
    {
        $this->impersonating = false;
        $user                = $this->user();

        if (null !== $user) {
            event(new Logout(self::class, $user));
        }

        $this->setCredential(null, $this->getCredentialSource());
        $this->pushState();
        $this->forgetUser();

        return $this;
    }

    public function processToken(
        string $token,
    ): ?array {
        $event = new TokenVerificationAttempting($token);
        event($event);
        $token = $event->getToken();

        try {
            $data = $this->getSdk()->decode(token: $token, tokenType: Token::TYPE_ACCESS_TOKEN)->toArray();
            event(new TokenVerificationSucceeded($token, $data));

            return $data;
        } catch (InvalidTokenException $invalidTokenException) {
            event(new TokenVerificationFailed($token, $invalidTokenException));

            return null;
        }
    }

    public function setCredential(
        ?Credential $credential = null,
        ?int $source = null,
    ): self {
        $this->credential       = $credential;
        $this->credentialSource = $source;

        return $this;
    }

    public function setImpersonating(
        bool $impersonate = false,
    ): self {
        $this->impersonating = $impersonate;

        return $this;
    }

    public function setUser(
        Authenticatable $user,
    ): void {
        $credential = $this->getCredential() ?? CredentialConcrete::create();
        $credential->setUser($user);

        $this->setCredential($credential);
        $this->pushState($credential);
    }

    public function user(): ?Authenticatable
    {
        $legacyBehavior = config('auth0.behavior.legacyGuardUserMethod', true);
        $currentUser    = $this->getCredential()?->getUser();

        if (null !== $currentUser) {
            return $currentUser;
        }

        // @codeCoverageIgnoreStart
        if (true === $legacyBehavior) {
            $token = $this->find(self::SOURCE_TOKEN);

            if (null !== $token) {
                $this->login($token, self::SOURCE_TOKEN);

                return $this->getCredential()?->getUser();
            }

            $session = $this->find(self::SOURCE_SESSION);

            if (null !== $session) {
                $this->login($token, self::SOURCE_SESSION);

                return $this->getCredential()?->getUser();
            }
        }
        // @codeCoverageIgnoreFalse

        return null;
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     *
     * @param array $credentials
     */
    public function validate(
        array $credentials = [],
    ): bool {
        return false;
    }

    public function viaRemember(): bool
    {
        return false;
    }
}
