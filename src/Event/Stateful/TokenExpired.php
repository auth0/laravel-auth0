<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

final class TokenExpired extends \Auth0\Laravel\Event\Auth0Event implements \Auth0\Laravel\Contract\Event\Stateful\TokenExpired
{
    /**
     * An instance of \Illuminate\Contracts\Auth\Authenticatable representing the authenticated user.
     */
    private \Illuminate\Contracts\Auth\Authenticatable $user;

    /**
     * Determines whether the local session will be cleared by the SDK after the event resolves.
     */
    private bool $clearSession;

    /**
     * @inheritdoc
     */
    public function __construct(
        \Illuminate\Contracts\Auth\Authenticatable $user,
        bool $clearSession = true
    ) {
        $this->user = $user;
        $this->clearSession = $clearSession;
    }

    /**
     * @inheritdoc
     */
    public function getUser(): \Illuminate\Contracts\Auth\Authenticatable
    {
        return $this->user;
    }

    /**
     * @inheritdoc
     */
    public function setClearSession(
        bool $clearSession
    ): self {
        $this->clearSession = $clearSession;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getClearSession(): bool
    {
        return $this->clearSession;
    }
}
