<?php

declare(strict_types=1);

namespace Auth0\Laravel\Auth\Guards;

use Auth0\Laravel\Auth\AbstractGuard;
use Auth0\Laravel\Contract\Auth\Guards\SessionGuardContract;
use Auth0\Laravel\Contract\Entities\CredentialContract;
use Auth0\Laravel\Entities\Credential;
use Auth0\Laravel\Event\Stateful\{TokenRefreshFailed, TokenRefreshSucceeded};
use Auth0\Laravel\Model\Stateful\User as StatefulUser;
use Auth0\SDK\Utility\HttpResponse;
use Illuminate\Auth\Events\{Login, Logout};
use Illuminate\Contracts\Auth\Authenticatable;

use function count;
use function is_array;
use function is_object;

final class SessionGuard extends AbstractGuard implements SessionGuardContract
{
    public function find(): ?CredentialContract
    {
        if ($this->isImpersonating()) {
            return $this->getImposter();
        }

        return $this->findSession();
    }

    public function findSession(): ?CredentialContract
    {
        if ($this->isImpersonating()) {
            return $this->getImposter();
        }

        $this->getSession();
        $session = $this->pullState();
        $user = $session?->getUser();

        if ($session instanceof CredentialContract && $user instanceof Authenticatable) {
            $user = $this->getProvider()->retrieveByCredentials($this->normalizeUserArray($user));

            if ($user instanceof Authenticatable) {
                $credential = Credential::create(
                    user: $user,
                    idToken: $session->getIdToken(),
                    accessToken: $session->getAccessToken(),
                    accessTokenDecoded: $session->getAccessTokenDecoded(),
                    accessTokenScope: $session->getAccessTokenScope(),
                    accessTokenExpiration: $session->getAccessTokenExpiration(),
                    refreshToken: $session->getRefreshToken(),
                );

                return $this->refreshSession($credential);
            }
        }

        return null;
    }

    public function getCredential(): ?CredentialContract
    {
        if ($this->isImpersonating()) {
            return $this->getImposter();
        }

        if ($this->credential instanceof CredentialContract) {
            $updated = $this->findSession();
            $this->setCredential($updated);
            $this->pushState($updated);
        }

        return $this->credential;
    }

    public function login(
        ?CredentialContract $credential,
    ): self {
        $this->stopImpersonating();

        $this->setCredential($credential);
        $this->pushState($credential);

        if ($credential instanceof CredentialContract) {
            $user = $credential->getUser();

            if ($user instanceof Authenticatable) {
                event(new Login(self::class, $user, true));
            }
        }

        return $this;
    }

    public function logout(): self
    {
        $user = $this->user();

        if ($user instanceof Authenticatable) {
            event(new Logout(self::class, $user));
        }

        $this->stopImpersonating();
        $this->setCredential();
        $this->pushState();
        $this->forgetUser();

        return $this;
    }

    public function pushState(
        ?CredentialContract $credential = null,
    ): self {
        $sdk = $this->sdk();
        $credential ??= $this->getCredential();

        if (! $credential instanceof CredentialContract) {
            $sdk->clear(true);

            return $this;
        }

        $user = $credential->getUser();
        $idToken = $credential->getIdToken();
        $accessToken = $credential->getAccessToken();
        $accessTokenScope = $credential->getAccessTokenScope();
        $accessTokenExpiration = $credential->getAccessTokenExpiration();
        $refreshToken = $credential->getRefreshToken();

        if ($user instanceof Authenticatable) {
            $update = $this->normalizeUserArray($user);
            $current = $sdk->getUser() ?? [];

            if (count($update) !== count($current) || [] !== array_diff($update, $current)) {
                $sdk->setUser($update);
            }
        }

        if (null !== $idToken && $idToken !== $sdk->getIdToken()) {
            $sdk->setIdToken($idToken);
        }

        if (null !== $accessToken && $accessToken !== $sdk->getAccessToken()) {
            $sdk->setAccessToken($accessToken);
        }

        if (null !== $accessTokenScope && $accessTokenScope !== $sdk->getAccessTokenScope()) {
            $sdk->setAccessTokenScope($accessTokenScope);
        }

        if (null !== $accessTokenExpiration && $accessTokenExpiration !== $sdk->getAccessTokenExpiration()) {
            $sdk->setAccessTokenExpiration($accessTokenExpiration);
        }

        if (null !== $refreshToken && $refreshToken !== $sdk->getRefreshToken()) {
            $sdk->setRefreshToken($refreshToken);
        }

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

                if (! is_array($response)) {
                    return;
                }

                $user = $this->getProvider()->retrieveByCredentials($response);

                $this->pushState(Credential::create(
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
        $this->pushState($credential);
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

        $session = $this->find();

        if ($session instanceof CredentialContract) {
            $this->login($session);

            return $this->getCredential()?->getUser();
        }

        return null;
    }

    private function pullState(): ?CredentialContract
    {
        $sdk = $this->sdk();
        $sdk->refreshState();

        $credentials = $sdk->getCredentials();

        /** @var mixed $credentials */
        if (is_object($credentials) && property_exists($credentials, 'user') && property_exists($credentials, 'idToken') && property_exists($credentials, 'accessToken') && property_exists($credentials, 'accessTokenScope') && property_exists($credentials, 'accessTokenExpiration') && property_exists($credentials, 'refreshToken')) {
            return Credential::create(
                user: new StatefulUser($credentials->user),
                idToken: $credentials->idToken,
                accessToken: $credentials->accessToken,
                accessTokenDecoded: $this->sdk()->decode($credentials->accessToken)?->toArray() ?? [],
                accessTokenScope: $credentials->accessTokenScope,
                accessTokenExpiration: $credentials->accessTokenExpiration,
                refreshToken: $credentials->refreshToken,
            );
        }

        return null;
    }

    private function refreshSession(
        ?CredentialContract $credential,
    ): ?CredentialContract {
        if (! $credential instanceof CredentialContract || true !== $credential->getAccessTokenExpired()) {
            return $credential;
        }

        if (null === $credential->getRefreshToken()) {
            return null;
        }

        try {
            $this->sdk()->renew();
            $session = $this->pullState();
        } catch (\Throwable) {
            event(new TokenRefreshFailed());
            $session = null;
        }

        if ($session instanceof CredentialContract) {
            event(new TokenRefreshSucceeded());

            $user = $this->getProvider()->retrieveByCredentials($this->normalizeUserArray($session->getUser()));

            if ($user instanceof Authenticatable) {
                return Credential::create(
                    user: $user,
                    idToken: $session->getIdToken(),
                    accessToken: $session->getAccessToken(),
                    accessTokenDecoded: $this->sdk()->decode($session->accessToken)?->toArray() ?? [],
                    accessTokenScope: $session->getAccessTokenScope(),
                    accessTokenExpiration: $session->getAccessTokenExpiration(),
                    refreshToken: $session->getRefreshToken(),
                );
            }
        }

        $this->setCredential(null);
        $this->pushState();

        return null;
    }
}
