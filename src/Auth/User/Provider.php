<?php

declare(strict_types=1);

namespace Auth0\Laravel\Auth\User;

use Auth0\Laravel\Contract\Auth\Guards\TokenGuardContract;
use Auth0\Laravel\Contract\Auth\User\{Provider as ProviderContract, Repository as RepositoryContract};
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Container\BindingResolutionException;

use function is_string;

final class Provider implements ProviderContract
{
    private ?RepositoryContract $repository = null;

    private string $repositoryName = '';

    public function __construct(
        private array $config = [],
    ) {
    }

    public function getRepository(): RepositoryContract
    {
        return $this->repository ?? $this->resolveRepository();
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     *
     * @param array $credentials
     */
    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        return $this->getRepository()->fromSession($credentials);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     *
     * @codeCoverageIgnore
     *
     * @param mixed $identifier
     */
    public function retrieveById($identifier): ?Authenticatable
    {
        return null;
    }

    /**
     * @psalm-suppress DocblockTypeContradiction
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     *
     * @param mixed $identifier
     * @param mixed $token
     */
    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        // @phpstan-ignore-next-line
        if (! is_string($token)) {
            return null;
        }

        $guard = auth()->guard();

        if (! $guard instanceof TokenGuardContract) {
            return null;
        }

        $user = $guard->processToken(
            token: $token,
        );

        if (null === $user) {
            return null;
        }

        return $this->getRepository()->fromAccessToken(
            user: $user,
        );
    }

    public function setRepository(string $repository): void
    {
        $this->resolveRepository($repository);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     *
     * @codeCoverageIgnore
     *
     * @param Authenticatable $user
     * @param mixed           $token
     */
    public function updateRememberToken(Authenticatable $user, $token): void
    {
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     *
     * @param Authenticatable $user
     * @param array           $credentials
     */
    public function validateCredentials(
        Authenticatable $user,
        array $credentials,
    ): bool {
        return false;
    }

    private function getConfiguration(
        string $key,
    ): array | string | null {
        return $this->config[$key] ?? null;
    }

    private function getRepositoryName(): string
    {
        return $this->repositoryName;
    }

    private function resolveRepository(
        ?string $repositoryName = null,
    ): RepositoryContract {
        $model = $repositoryName;
        $model ??= $this->getConfiguration('model');
        $model ??= $this->getConfiguration('repository');
        $model ??= Repository::class;

        if ($model === $this->getRepositoryName()) {
            return $this->getRepository();
        }

        if (! is_string($model)) {
            throw new BindingResolutionException('The configured Repository could not be loaded.');
        }

        if (! app()->bound($model)) {
            try {
                app()->make($model);
            } catch (BindingResolutionException) {
                throw new BindingResolutionException(sprintf('The configured Repository %s could not be loaded.', $model));
            }
        }

        $this->setRepositoryName($model);

        return $this->repository = app($model);
    }

    private function setConfiguration(
        string $key,
        mixed $value,
    ): void {
        $this->config[$key] = $value;
    }

    private function setRepositoryName(string $repositoryName): void
    {
        $this->setConfiguration('model', $repositoryName);
        $this->repositoryName = $repositoryName;
    }
}
