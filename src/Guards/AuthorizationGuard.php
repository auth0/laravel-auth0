<?php

declare(strict_types=1);

namespace Auth0\Laravel\Guards;

use Auth0\Laravel\Entities\{CredentialEntity, CredentialEntityContract};
use Auth0\Laravel\UserProviderContract;
use Auth0\SDK\Utility\HttpResponse;
use Illuminate\Contracts\Auth\Authenticatable;

use function is_array;
use function is_string;

/**
 * Authorization guard for stateless token-based authentication.
 *
 * @api
 */
final class AuthorizationGuard extends GuardAbstract implements AuthorizationGuardContract
{
    public function find(): ?CredentialEntityContract
    {
        if ($this->isImpersonating()) {
            return $this->getImposter();
        }

        return $this->findToken();
    }

    public function findToken(): ?CredentialEntityContract
    {
        if ($this->isImpersonating()) {
            return $this->getImposter();
        }

        $token = trim(app('request')->bearerToken() ?? '');

        if ('' === $token) {
            return null;
        }

        $decoded = $this->processToken(
            token: $token,
        );

        /**
         * @var null|array<string> $decoded
         */
        if (null === $decoded) {
            return null;
        }

        $provider = $this->getProvider();

        // @codeCoverageIgnoreStart
        if (! $provider instanceof UserProviderContract) {
            return null;
        }
        // @codeCoverageIgnoreEnd

        $user = $provider->getRepository()->fromAccessToken(
            user: $decoded,
        );

        // @codeCoverageIgnoreStart
        if (! $user instanceof Authenticatable) {
            return null;
        }
        // @codeCoverageIgnoreEnd

        $data = $this->normalizeUserArray($user);

        // @codeCoverageIgnoreStart
        if ([] === $data) {
            return null;
        }
        // @codeCoverageIgnoreEnd

        $scope = isset($data['scope']) && is_string($data['scope']) ? explode(' ', $data['scope']) : [];
        $exp = isset($data['exp']) && is_numeric($data['exp']) ? (int) $data['exp'] : null;

        return CredentialEntity::create(
            user: $user,
            accessToken: $token,
            accessTokenScope: $scope,
            accessTokenExpiration: $exp,
            accessTokenDecoded: $decoded,
        );
    }

    public function forgetUser(): self
    {
        $this->setCredential();

        return $this;
    }

    public function getCredential(): ?CredentialEntityContract
    {
        if ($this->isImpersonating()) {
            return $this->getImposter();
        }

        return $this->credential;
    }

    public function login(
        ?CredentialEntityContract $credential,
    ): self {
        $this->stopImpersonating();

        $this->setCredential($credential);

        return $this;
    }

    public function logout(): self
    {
        $this->stopImpersonating();

        $this->setCredential();
        $this->forgetUser();

        return $this;
    }

    public function refreshUser(): void
    {
        if ($this->isImpersonating()) {
            return;
        }

        if ($this->check()) {
            $credential = $this->getCredential();
            $accessToken = $credential?->getAccessToken();

            if (! $credential instanceof CredentialEntityContract || null === $accessToken) {
                return;
            }

            $response = $this->sdk()->authentication()->userInfo($accessToken);

            if (HttpResponse::wasSuccessful($response)) {
                $response = HttpResponse::decodeContent($response);

                if (! is_array($response)) {
                    return;
                }

                $user = $this->getProvider()->retrieveByCredentials($response);
                $scope = $credential->getAccessTokenScope();

                /**
                 * @var array<string> $scope
                 */
                $this->setCredential(CredentialEntity::create(
                    user: $user,
                    idToken: $credential->getIdToken(),
                    accessToken: $credential->getAccessToken(),
                    accessTokenScope: $scope,
                    accessTokenExpiration: $credential->getAccessTokenExpiration(),
                    refreshToken: $credential->getRefreshToken(),
                ));
            }
        }
    }

    public function setCredential(
        ?CredentialEntityContract $credential = null,
    ): self {
        $this->stopImpersonating();

        $this->credential = $credential;

        return $this;
    }

    /**
     * @param CredentialEntityContract $credential
     */
    public function setImpersonating(
        CredentialEntityContract $credential,
    ): self {
        $this->impersonationSource = self::SOURCE_TOKEN;
        $this->impersonating = $credential;

        return $this;
    }

    public function setUser(
        Authenticatable $user,
    ): void {
        if ($this->isImpersonating()) {
            if ($this->getImposter()?->getUser() === $user) {
                return;
            }

            $this->stopImpersonating();
        }

        $credential = $this->getCredential() ?? CredentialEntity::create();
        $credential->setUser($user);

        $this->setCredential($credential);
    }

    public function user(): ?Authenticatable
    {
        if ($this->isImpersonating()) {
            return $this->getImposter()?->getUser();
        }

        $currentUser = $this->getCredential()?->getUser();

        if ($currentUser instanceof Authenticatable) {
            return $currentUser;
        }

        $token = $this->find();

        if ($token instanceof CredentialEntityContract) {
            $this->login($token);

            return $this->getCredential()?->getUser();
        }

        return null;
    }
}
