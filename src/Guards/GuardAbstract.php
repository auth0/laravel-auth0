<?php

declare(strict_types=1);

namespace Auth0\Laravel\Guards;

use Auth0\Laravel\Configuration;
use Auth0\Laravel\Entities\{CredentialEntityContract, InstanceEntity, InstanceEntityContract};
use Auth0\Laravel\Exceptions\{AuthenticationException, GuardException, GuardExceptionContract};
use Auth0\SDK\Contract\API\ManagementInterface;
use Auth0\SDK\Contract\Auth0Interface;
use Exception;
use Illuminate\Contracts\Auth\{Authenticatable, UserProvider};
use Illuminate\Contracts\Session\Session;

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
abstract class GuardAbstract implements GuardContract
{
    private ?int $impersonationSource = null;

    protected ?CredentialEntityContract $credential = null;

    protected ?CredentialEntityContract $impersonating = null;

    protected ?UserProvider $provider = null;

    protected ?InstanceEntityContract $sdk = null;

    public function __construct(
        public string $name = '',
        protected ?array $config = null,
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

    final public function forgetUser(): GuardContract
    {
        $this->setCredential();

        return $this;
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
        $store = app('session.store');
        $request = app('request');

        if (! $request->hasSession(true)) {
            $request->setLaravelSession($store);
        }

        if (! $store->isStarted()) {
            $store->start();
        }

        return $store;
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
        if (is_array($available) && [] !== $available) {
            return in_array($permission, $available, true);
        }

        return false;
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

    final public function sdk(
        $reset = false,
    ): Auth0Interface {
        if (! $this->sdk instanceof InstanceEntityContract || true === $reset) {
            $configuration = [];

            if (2 === Configuration::version()) {
                /**
                 * @var string $configName
                 */
                $configName = $this->config['configuration'] ?? $this->name;

                $defaultConfiguration = config('auth0.default') ?? [];
                $guardConfiguration = config('auth0.' . $configName) ?? [];
                $configuration = array_merge($defaultConfiguration, $guardConfiguration);
            }

            // Fallback to the legacy configuration format if a version is not defined.
            if (2 !== Configuration::version()) {
                $configuration = config('auth0');
            }

            $this->sdk = InstanceEntity::create($configuration);
        }

        return $this->sdk->getSdk();
    }

    final public function setImpersonating(
        CredentialEntityContract $credential,
        ?int $source = null,
    ): GuardContract {
        $this->impersonationSource = $source;
        $this->impersonating = $credential;

        return $this;
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

    abstract public function getCredential(): ?CredentialEntityContract;

    abstract public function login(?CredentialEntityContract $credential): GuardContract;

    abstract public function refreshUser(): void;

    abstract public function setCredential(?CredentialEntityContract $credential = null): GuardContract;

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
     * @psalm-suppress TypeDoesNotContainType, UndefinedDocblockClass
     *
     * @codeCoverageIgnore
     */
    protected function normalizeUserArray(
        Authenticatable $user,
    ): array {
        $implements = class_implements($user);

        if (in_array('JsonSerializable', $implements, true) && method_exists($user, 'jsonSerialize')) {
            /**
             * @var JsonSerializable $user
             */
            $user = (array) $user->jsonSerialize();

            try {
                return json_decode(json_encode($user, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                throw new GuardException(GuardExceptionContract::USER_MODEL_NORMALIZATION_FAILURE);
            }
        }

        if (in_array('Illuminate\Contracts\Support\Arrayable', $implements, true) && method_exists($user, 'toArray')) {
            /**
             * @var \Illuminate\Contracts\Support\Arrayable $user
             */
            $user = (array) $user->toArray();

            try {
                return json_decode(json_encode($user, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                throw new GuardException(GuardExceptionContract::USER_MODEL_NORMALIZATION_FAILURE);
            }
        }

        if (in_array('Illuminate\Contracts\Support\Jsonable', $implements, true) && method_exists($user, 'toJson')) {
            /**
             * @var \Illuminate\Contracts\Support\Jsonable $user
             */
            $user = (array) $user->toJson();

            try {
                return json_decode(json_encode($user, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                throw new GuardException(GuardExceptionContract::USER_MODEL_NORMALIZATION_FAILURE);
            }
        }

        if (in_array('Illuminate\Database\Eloquent\Concerns\HasAttributes', $implements, true) && method_exists($user, 'attributesToArray')) {
            /**
             * @var \Illuminate\Database\Eloquent\Concerns\HasAttributes $user
             */
            $user = (array) $user->attributesToArray();

            try {
                return json_decode(json_encode($user, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                throw new GuardException(GuardExceptionContract::USER_MODEL_NORMALIZATION_FAILURE);
            }
        }

        throw new GuardException(GuardExceptionContract::USER_MODEL_NORMALIZATION_FAILURE);
    }
}
