<?php

declare(strict_types=1);

namespace Auth0\Laravel\Auth;

use Auth0\Laravel\Contract\Auth\GuardContract;
use Auth0\Laravel\Contract\Entities\CredentialContract;
use Auth0\Laravel\Contract\Exception\GuardException as GuardExceptionContract;
use Auth0\Laravel\Entities\{Configuration, ConfigurationContract, Credential};
use Auth0\Laravel\Exception\{AuthenticationException, GuardException};
use Auth0\SDK\Contract\API\ManagementInterface;
use Auth0\SDK\Contract\Auth0Interface;
use Auth0\Laravel\Configuration as ConfigurationHelper;
use Exception;
use Illuminate\Contracts\Auth\{Authenticatable, UserProvider};
use Illuminate\Contracts\Session\Session;

use function in_array;
use function is_array;
use function is_int;
use function is_string;

abstract class AbstractGuard implements GuardContract
{
    private ?int $impersonationSource = null;

    protected ?CredentialContract $credential = null;

    protected ?CredentialContract $impersonating = null;

    protected ?UserProvider $provider = null;

    protected ?ConfigurationContract $sdk = null;

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

    final public function getImposter(): ?CredentialContract
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
        ?CredentialContract $credential = null,
    ): bool {
        $permission = trim($permission);

        if ('*' === $permission) {
            return true;
        }

        $available = $credential?->getAccessTokenDecoded() ?? $this->getCredential()?->getAccessTokenDecoded() ?? [];
        $available = $available['permissions'] ?? [];

        if (is_array($available) && [] !== $available) {
            return in_array($permission, $available, true);
        }

        return false;
    }

    final public function hasScope(
        string $scope,
        ?CredentialContract $credential = null,
    ): bool {
        $scope = trim($scope);

        if ('*' === $scope) {
            return true;
        }

        $available = $credential?->getAccessTokenScope() ?? $this->getCredential()?->getAccessTokenScope() ?? [];

        if (is_array($available) && [] !== $available) {
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
        return $this->impersonating instanceof CredentialContract;
    }

    final public function management(): ManagementInterface
    {
        return $this->sdk()->management();
    }

    final public function sdk(
        $reset = false,
    ): Auth0Interface {
        if (! $this->sdk instanceof ConfigurationContract || true === $reset) {
            $configuration = [];

            if (ConfigurationHelper::version() === ConfigurationHelper::VERSION_2) {
                $configuration = $this->config['configuration'] ?? $this->name;

                $defaultConfiguration = config('auth0.default') ?? [];
                $guardConfiguration = config('auth0.' . $configuration) ?? [];
                $configuration = array_merge($defaultConfiguration, $guardConfiguration);
            }

            // Fallback to the legacy configuration format if a version is not defined.
            if (ConfigurationHelper::version() !== ConfigurationHelper::VERSION_2) {
                $configuration = config('auth0');
            }

            $this->sdk = Configuration::create($configuration);
        }

        return $this->sdk->getSdk();
    }

    final public function setImpersonating(
        CredentialContract $credential,
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

    /**
     * Searches for an available credential from a specified source in the current request context.
     */
    abstract public function find(): ?CredentialContract;

    abstract public function getCredential(): ?CredentialContract;

    /**
     * Sets the currently authenticated user for the guard.
     *
     * @param null|Credential $credential Optional. The credential to use.
     */
    abstract public function login(?CredentialContract $credential): GuardContract;

    abstract public function refreshUser(): void;

    /**
     * Sets the guard's currently configured credential.
     *
     * @param null|Credential $credential Optional. The credential to assign.
     */
    abstract public function setCredential(?CredentialContract $credential = null): GuardContract;

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
     * @psalm-suppress TypeDoesNotContainType
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
             * @var Illuminate\Contracts\Support\Arrayable $user
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
             * @var Illuminate\Contracts\Support\Jsonable $user
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
             * @var Illuminate\Database\Eloquent\Concerns\HasAttributes $user
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
