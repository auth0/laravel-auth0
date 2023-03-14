<?php

declare(strict_types=1);

namespace Auth0\Laravel\Traits;

use Auth0\Laravel\Contract\Auth\Guard;
use Auth0\Laravel\Entities\Credential;
use Auth0\Laravel\Model\Stateless\User;
use Illuminate\Contracts\Auth\Authenticatable;

trait Impersonate
{
    /**
     * Set the currently logged in user for the application.
     *
     * @param Credential  $credential The Credential to impersonate.
     * @param null|int    $source     The source of the Credential.
     * @param null|string $guard      The guard to impersonate with.
     *
     * @return $this The current test case instance.
     */
    public function impersonate(
        Credential $credential,
        ?int $source = null,
        ?string $guard = null,
    ): self {
        $instance = auth()->guard($guard);
        $user     = $credential->getUser() ?? new User([]);

        if ($instance instanceof Guard) {
            $instance->setCredential($credential, $source);
            $instance->setImpersonating(true);
        }

        return $this->actingAs($user, $guard);
    }

    abstract public function actingAs(Authenticatable $user, $guard = null);
}
