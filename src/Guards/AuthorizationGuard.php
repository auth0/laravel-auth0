<?php

declare(strict_types=1);

namespace Auth0\Laravel\Guards;

use Auth0\Laravel\Entities\{CredentialEntity, CredentialEntityContract};
use Auth0\Laravel\Events\{TokenVerificationAttempting, TokenVerificationFailed, TokenVerificationSucceeded};
use Auth0\Laravel\UserProviderContract;
use Auth0\SDK\Exception\InvalidTokenException;
use Auth0\SDK\Token;
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

        if (null === $decoded) {
            return null;
        }

        $provider = $this->getProvider();

        if (! $provider instanceof UserProviderContract) {
            return null;
        }

        $user = $provider->getRepository()->fromAccessToken(
            user: $decoded,
        );

        if ($user instanceof Authenticatable) {
            $data = $this->normalizeUserArray($user);

            if ([] !== $data) {
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
        }

        return null;
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

    public function processToken(
        string $token,
    ): ?array {
        $event = new TokenVerificationAttempting($token);
        event($event);
        $token = $event->getToken();

        try {
            $data = $this->sdk()->decode(token: $token, tokenType: Token::TYPE_ACCESS_TOKEN)->toArray();
            event(new TokenVerificationSucceeded($token, $data));

            return $data;
        } catch (InvalidTokenException $invalidTokenException) {
            event(new TokenVerificationFailed($token, $invalidTokenException));

            return null;
        }
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

                $this->setCredential(CredentialEntity::create(
                    user: $user,
                    idToken: $credential->getIdToken(),
                    accessToken: $credential->getAccessToken(),
                    accessTokenScope: $credential->getAccessTokenScope(),
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
