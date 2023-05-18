<?php

declare(strict_types=1);

namespace Auth0\Laravel\Auth;

use Auth0\Laravel\Entities\CredentialEntityContract;
use Auth0\Laravel\Guards\{AuthenticationGuard, AuthenticationGuardContract, AuthorizationGuard, AuthorizationGuardContract, GuardAbstract, GuardContract};
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * @deprecated 7.8.0 Please migrate to using either Auth0\Laravel\Guards\AuthenticationGuard or Auth0\Laravel\Guards\AuthorizationGuard.
 *
 * @api
 */
final class Guard extends GuardAbstract implements GuardContract
{
    private ?AuthenticationGuardContract $authenticator = null;

    private ?AuthorizationGuardContract $authorizer = null;

    private ?int $credentialSource = null;

    /**
     * @param null|int $source Credential source in which to search. Defaults to searching all sources.
     */
    public function find(
        ?int $source = null,
    ): ?CredentialEntityContract {
        $token = null;
        $session = null;

        if ($this->isImpersonating()) {
            return $this->getImposter();
        }

        if (null === $source || self::SOURCE_TOKEN === $source) {
            $token = $this->getAuthorizationGuard()->findToken();
        }

        if (null === $source && ! $token instanceof CredentialEntityContract || self::SOURCE_SESSION === $source) {
            $session = $this->getAuthenticationGuard()->findSession();
        }

        return $token ?? $session ?? null;
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

        $token = null;
        $session = null;
        $source = $this->getCredentialSource();

        if (null === $source || self::SOURCE_TOKEN === $source) {
            $token = $this->getAuthorizationGuard()->getCredential();
        }

        if (null === $source && ! $token instanceof CredentialEntityContract || self::SOURCE_SESSION === $source) {
            $session = $this->getAuthenticationGuard()->getCredential();
        }

        return $token ?? $session ?? null;
    }

    /**
     * Sets the currently authenticated user for the guard.
     *
     * @param null|CredentialEntityContract $credential Optional. The credential to use.
     * @param null|int                      $source     Optional. The source context in which to assign the user. Defaults to all sources.
     */
    public function login(
        ?CredentialEntityContract $credential,
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
        ?CredentialEntityContract $credential = null,
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

    /**
     * @param CredentialEntityContract $credential
     * @param ?int                     $source
     */
    public function setImpersonating(
        CredentialEntityContract $credential,
        ?int $source = null,
    ): self {
        $this->impersonationSource = $source;
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
            return $this->getImposter()?->getUser();
        }

        $credential = $this->getCredential();

        if ($credential instanceof CredentialEntityContract) {
            return $credential->getUser();
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

    private function getAuthenticationGuard(): AuthenticationGuardContract
    {
        $this->sdk();

        return $this->authenticator ??= new AuthenticationGuard(name: $this->name, config: $this->config, sdk: $this->sdk);
    }

    private function getAuthorizationGuard(): AuthorizationGuardContract
    {
        $this->sdk();

        return $this->authorizer ??= new AuthorizationGuard(name: $this->name, config: $this->config, sdk: $this->sdk);
    }

    private function getCredentialSource(): ?int
    {
        return $this->credentialSource;
    }
}
