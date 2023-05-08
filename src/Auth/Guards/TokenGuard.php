<?php

declare(strict_types=1);

namespace Auth0\Laravel\Auth\Guards;

use Auth0\Laravel\Auth\AbstractGuard;
use Auth0\Laravel\Contract\Auth\Guards\TokenGuardContract;
use Auth0\Laravel\Contract\Auth\User\Provider;
use Auth0\Laravel\Contract\Entities\CredentialContract;
use Auth0\Laravel\Entities\Credential;
use Auth0\Laravel\Event\Stateless\TokenVerificationAttempting;
use Auth0\Laravel\Event\Stateless\TokenVerificationFailed;
use Auth0\Laravel\Event\Stateless\TokenVerificationSucceeded;
use Auth0\SDK\Exception\InvalidTokenException;
use Auth0\SDK\Token;
use Auth0\SDK\Utility\HttpResponse;
use Illuminate\Contracts\Auth\Authenticatable;

use function is_array;
use function is_string;

final class TokenGuard extends AbstractGuard implements TokenGuardContract
{
    public function find(): ?CredentialContract
    {
        if ($this->isImpersonating()) {
            return $this->getImposter();
        }

        return $this->findToken();
    }

    public function findToken(): ?CredentialContract
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

        if (! $provider instanceof Provider) {
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

                return Credential::create(
                    user: $user,
                    accessToken: $token,
                    accessTokenScope: $scope,
                    accessTokenExpiration: $exp,
                    accessTokenDecoded: $decoded
                );
            }
        }

        return null;
    }

    public function getCredential(): ?CredentialContract
    {
        if ($this->isImpersonating()) {
            return $this->getImposter();
        }

        return $this->credential;
    }

    public function login(
        ?CredentialContract $credential,
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

            if (! $credential instanceof CredentialContract || null === $accessToken) {
                return;
            }

            $response = $this->sdk()->authentication()->userInfo($accessToken);

            if (HttpResponse::wasSuccessful($response)) {
                $response = HttpResponse::decodeContent($response);

                var_dump($response); exit;

                if (! is_array($response)) {
                    return;
                }

                $user = $this->getProvider()->retrieveByCredentials($response);

                $this->setCredential(Credential::create(
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
        ?CredentialContract $credential = null,
    ): self {
        $this->stopImpersonating();

        $this->credential = $credential;

        return $this;
    }

    public function setUser(
        Authenticatable $user,
    ): void {
        if ($this->isImpersonating()) {
            if ($this->getImposter()->getUser() === $user) {
                return;
            }

            $this->stopImpersonating();
        }

        $credential = $this->getCredential() ?? Credential::create();
        $credential->setUser($user);

        $this->setCredential($credential);
    }

    public function user(): ?Authenticatable
    {
        if ($this->isImpersonating()) {
            return $this->getImposter()->getUser();
        }

        $currentUser = $this->getCredential()?->getUser();

        if ($currentUser instanceof Authenticatable) {
            return $currentUser;
        }

        $token = $this->find();

        if ($token instanceof CredentialContract) {
            $this->login($token);

            return $this->getCredential()?->getUser();
        }

        return null;
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
}
