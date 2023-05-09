<?php

declare(strict_types=1);

namespace Auth0\Laravel\Auth;

use Auth0\Laravel\Auth\Guards\{SessionGuard, TokenGuard};
use Auth0\Laravel\Contract\Auth\GuardContract;
use Auth0\Laravel\Contract\Auth\Guards\{SessionGuardContract, TokenGuardContract};
use Auth0\Laravel\Contract\Entities\CredentialContract;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * @deprecated 7.8.0 Use either Auth0\Laravel\Auth\Guards\SessionGuard or Auth0\Laravel\Auth\Guards\TokenGuard instead.
 */
final class Guard extends AbstractGuard implements GuardContract
{
    /**
     * @var int Credential source is a stateful session.
     */
    public const SOURCE_SESSION = 2;

    /**
     * @var int Credential source is a stateless token.
     */
    public const SOURCE_TOKEN = 1;

    private ?int $credentialSource = null;

    private ?SessionGuardContract $sessionGuard = null;

    private ?TokenGuardContract $tokenGuard = null;

    /**
     * @param null|int $source Credential source in which to search. Defaults to searching all sources.
     */
    public function find(
        ?int $source = null,
    ): ?CredentialContract {
        $token = null;
        $session = null;

        if ($this->isImpersonating()) {
            return $this->getImposter();
        }

        if (null === $source || self::SOURCE_TOKEN === $source) {
            $token = $this->getAuthorizationGuard()->findToken();
        }

        if (null === $source || self::SOURCE_SESSION === $source) {
            $session = $this->getAuthenticationGuard()->findSession();
        }

        return $token ?? $session ?? null;
    }

    public function getCredential(): ?CredentialContract
    {
        if ($this->isImpersonating()) {
            return $this->getImposter();
        }

        $token = null;
        $session = null;
        $source = $this->getCredentialSource();

        if (null === $source || self::SOURCE_TOKEN === $source) {
            $token = $this->getAuthorizationGuard()->getCredential();
        }

        if (null === $source || self::SOURCE_SESSION === $source) {
            $session = $this->getAuthenticationGuard()->getCredential();
        }

        return $token ?? $session ?? null;
    }

    public function login(
        ?CredentialContract $credential,
        ?int $source = null,
    ): GuardContract {
        $this->stopImpersonating();

        if (null === $source || self::SOURCE_TOKEN === $source) {
            $this->getAuthorizationGuard()->login($credential);
        }

        if (null === $source || self::SOURCE_SESSION === $source) {
            $this->getAuthenticationGuard()->login($credential);
        }

        $this->credentialSource = $source;

        return $this;
    }

    public function logout(): GuardContract
    {
        if ($this->isImpersonating()) {
            $this->stopImpersonating();

            return $this;
        }

        $source = $this->getCredentialSource();

        if (null === $source || self::SOURCE_TOKEN === $source) {
            $this->getAuthorizationGuard()->logout();
        }

        if (null === $source || self::SOURCE_SESSION === $source) {
            $this->getAuthenticationGuard()->logout();
        }

        return $this;
    }

    public function refreshUser(): void
    {
        if ($this->isImpersonating()) {
            return;
        }

        $source = $this->getCredentialSource();

        if (null === $source || self::SOURCE_TOKEN === $source) {
            $this->getAuthorizationGuard()->refreshUser();
        }

        if (null === $source || self::SOURCE_SESSION === $source) {
            $this->getAuthenticationGuard()->refreshUser();
        }
    }

    public function setCredential(
        ?CredentialContract $credential = null,
        ?int $source = null,
    ): GuardContract {
        $this->stopImpersonating();

        if (null === $source || self::SOURCE_TOKEN === $source) {
            $this->getAuthorizationGuard()->setCredential($credential);
        }

        if (null === $source || self::SOURCE_SESSION === $source) {
            $this->getAuthenticationGuard()->setCredential($credential);
        }

        $this->credentialSource = $source;

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

        $source = $this->getCredentialSource();

        if (null === $source || self::SOURCE_TOKEN === $source) {
            $this->getAuthorizationGuard()->setUser($user);
        }

        if (null === $source || self::SOURCE_SESSION === $source) {
            $this->getAuthenticationGuard()->setUser($user);
        }
    }

    public function user(): ?Authenticatable
    {
        if ($this->isImpersonating()) {
            return $this->getImposter()->getUser();
        }

        if ($this->getCredential() instanceof CredentialContract) {
            return $this->getCredential()->getUser();
        }

        // $source = $this->getCredentialSource();
        // $token = null;
        // $session = null;

        // if (null === $source || self::SOURCE_TOKEN === $source) {
        //     $token = $this->getAuthorizationGuard()->user();
        // }

        // if (null === $source || self::SOURCE_SESSION === $source) {
        //     $session = $this->getAuthenticationGuard()->user();
        // }

        // return $token ?? $session ?? null;

        return null;
    }

    private function getAuthenticationGuard(): SessionGuardContract
    {
        return $this->sessionGuard ??= new SessionGuard(name: $this->name, config: $this->config);
    }

    private function getAuthorizationGuard(): TokenGuardContract
    {
        return $this->tokenGuard ??= new TokenGuard(name: $this->name, config: $this->config);
    }

    private function getCredentialSource(): ?int
    {
        return $this->credentialSource;
    }
}
