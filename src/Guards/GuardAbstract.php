<?php

declare(strict_types=1);

namespace Auth0\Laravel\Guards;

use Auth0\Laravel\Entities\{CredentialEntityContract, InstanceEntity, InstanceEntityContract};
use Auth0\Laravel\Events;
use Auth0\Laravel\Events\{TokenVerificationAttempting, TokenVerificationFailed, TokenVerificationSucceeded};
use Auth0\Laravel\Exceptions\{AuthenticationException, GuardException, GuardExceptionContract};
use Auth0\SDK\Contract\API\ManagementInterface;
use Auth0\SDK\Contract\Auth0Interface;
use Auth0\SDK\Exception\InvalidTokenException;
use Auth0\SDK\Token;
use Exception;
use Illuminate\Contracts\Auth\{Authenticatable, Guard, UserProvider};
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Support\{Arrayable, Jsonable};
use JsonSerializable;

use function in_array;
use function is_array;
use function is_int;
use function is_string;

/**
 * @internal
 *
 * @api
 */
abstract class GuardAbstract implements Guard
{
    protected ?CredentialEntityContract $credential = null;

    protected ?CredentialEntityContract $impersonating = null;

    protected ?int $impersonationSource = null;

    protected ?UserProvider $provider = null;

    protected ?Session $session = null;

    public function __construct(
        public string $name = '',
        protected ?array $config = null,
        protected ?InstanceEntityContract $sdk = null,
    ) {
    }

    final public function authenticate(): Authenticatable
    {
        if (($user = $this->user()) instanceof Authenticatable) {
            return $user;
        }

        throw new AuthenticationException(AuthenticationException::UNAUTHENTICATED);
    }

    final public function check(): bool
    {
        return $this->hasUser();
    }

    final public function getImposter(): ?CredentialEntityContract
    {
        return $this->impersonating;
    }

    final public function getImposterSource(): ?int
    {
        return $this->impersonationSource;
    }

    final public function getName(): string
    {
        return $this->name;
    }

    final public function getProvider(): UserProvider
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
        $provider = app('auth')->createUserProvider($providerName);

        if ($provider instanceof UserProvider) {
            $this->provider = $provider;

            return $provider;
        }

        // @codeCoverageIgnoreStart
        throw new GuardException(sprintf(GuardExceptionContract::USER_PROVIDER_UNAVAILABLE, $providerName));
        // @codeCoverageIgnoreEnd
    }

    final public function getRefreshedUser(): ?Authenticatable
    {
        $this->refreshUser();

        return $this->user();
    }

    final public function getSession(): Session
    {
        if (! $this->session instanceof Session) {
            $store = app('session.store');
            $request = app('request');

            if (! $request->hasSession(true)) {
                $request->setLaravelSession($store);
            }

            if (! $store->isStarted()) {
                $store->start();
            }

            $this->session = $store;
        }

        return $this->session;
    }

    final public function guest(): bool
    {
        return ! $this->check();
    }

    final public function hasPermission(
        string $permission,
        ?CredentialEntityContract $credential = null,
    ): bool {
        $permission = trim($permission);

        if ('*' === $permission) {
            return true;
        }

        $available = $credential?->getAccessTokenDecoded() ?? $this->getCredential()?->getAccessTokenDecoded() ?? [];
        $available = $available['permissions'] ?? [];

        /**
         * @var mixed $available
         */
        if (! is_array($available) || [] === $available) {
            return false;
        }

        return in_array($permission, $available, true);
    }

    final public function hasScope(
        string $scope,
        ?CredentialEntityContract $credential = null,
    ): bool {
        $scope = trim($scope);

        if ('*' === $scope) {
            return true;
        }

        $available = $credential?->getAccessTokenScope() ?? $this->getCredential()?->getAccessTokenScope() ?? [];

        if ([] !== $available) {
            return in_array($scope, $available, true);
        }

        return false;
    }

    final public function hasUser(): bool
    {
        return $this->user() instanceof Authenticatable;
    }

    final public function id(): string | null
    {
        $user = $this->user()?->getAuthIdentifier();

        if (is_string($user) || is_int($user)) {
            return (string) $user;
        }

        return null;
    }

    final public function isImpersonating(): bool
    {
        return $this->impersonating instanceof CredentialEntityContract;
    }

    final public function management(): ManagementInterface
    {
        return $this->sdk()->management();
    }

    final public function processToken(
        string $token,
    ): ?array {
        Events::dispatch($event = new TokenVerificationAttempting($token));
        $token = $event->token;
        $decoded = null;

        try {
            $decoded = $this->sdk()->decode(token: $token, tokenType: Token::TYPE_ACCESS_TOKEN)->toArray();
        } catch (InvalidTokenException $invalidTokenException) {
            Events::dispatch($event = new TokenVerificationFailed($token, $invalidTokenException));

            if ($event->throwException) {
                // @codeCoverageIgnoreStart
                throw $invalidTokenException;
                // @codeCoverageIgnoreEnd
            }

            return null;
        }

        Events::dispatch(new TokenVerificationSucceeded($token, $decoded));

        return $decoded;
    }

    final public function sdk(
        bool $reset = false,
    ): Auth0Interface {
        if (! $this->sdk instanceof InstanceEntityContract || $reset) {
            $configurationName = $this->config['configuration'] ?? $this->name;

            $this->sdk = InstanceEntity::create(
                guardConfigurationName: $configurationName,
            );
        }

        return $this->sdk->getSdk();
    }

    /**
     * @codeCoverageIgnore
     */
    final public function service(): ?InstanceEntityContract
    {
        return $this->sdk;
    }

    final public function stopImpersonating(): void
    {
        $this->impersonating = null;
        $this->impersonationSource = null;
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     *
     * @param array $credentials
     */
    final public function validate(
        array $credentials = [],
    ): bool {
        return false;
    }

    final public function viaRemember(): bool
    {
        return false;
    }

    abstract public function find(): ?CredentialEntityContract;

    abstract public function forgetUser(): self;

    abstract public function getCredential(): ?CredentialEntityContract;

    abstract public function login(?CredentialEntityContract $credential): GuardContract;

    abstract public function logout(): GuardContract;

    abstract public function refreshUser(): void;

    abstract public function setCredential(?CredentialEntityContract $credential = null): GuardContract;

    /**
     * Toggle the Guard's impersonation state. This should only be used by the Impersonate trait, and is not intended for use by end-users. It is public to allow for testing.
     *
     * @param CredentialEntityContract $credential
     */
    abstract public function setImpersonating(
        CredentialEntityContract $credential,
    ): self;

    abstract public function setUser(
        Authenticatable $user,
    ): void;

    abstract public function user(): ?Authenticatable;

    /**
     * Normalize a user model object for easier storage or comparison.
     *
     * @param Authenticatable $user User model object.
     *
     * @throws Exception If the user model object cannot be normalized.
     *
     * @return array<array|int|string> Normalized user model object.
     *
     * @psalm-suppress TypeDoesNotContainType, UndefinedDocblockClass, UndefinedInterfaceMethod
     *
     * @codeCoverageIgnore
     */
    protected function normalizeUserArray(
        Authenticatable $user,
    ): array {
        $response = null;
        $implements = class_implements($user, true);
        $implements = is_array($implements) ? $implements : [];

        if (isset($implements[JsonSerializable::class])) {
            /**
             * @var JsonSerializable $user
             */
            $response = json_encode($user->jsonSerialize(), JSON_THROW_ON_ERROR);
        }

        if (null === $response && isset($implements[Jsonable::class])) {
            /**
             * @var Jsonable $user
             */
            $response = $user->toJson();
        }

        if (null === $response && isset($implements[Arrayable::class])) {
            /**
             * @var Arrayable $user
             */
            try {
                $response = json_encode($user->toArray(), JSON_THROW_ON_ERROR);
            } catch (Exception) {
            }
        }

        // if (null === $response && (new ReflectionClass($user))->hasMethod('attributesToArray')) {
        //     try {
        //         // @phpstan-ignore-next-line
        //         $response = json_encode($user->attributesToArray(), JSON_THROW_ON_ERROR);
        //     } catch (\Exception) {
        //     }
        // }

        if (is_string($response)) {
            try {
                $response = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

                if (is_array($response) && [] !== $response) {
                    /**
                     * @var array<array|int|string> $response
                     */
                    return $response;
                }
            } catch (Exception) {
            }
        }

        throw new GuardException(GuardExceptionContract::USER_MODEL_NORMALIZATION_FAILURE);
    }
}
