<?php

declare(strict_types=1);

namespace Auth0\Laravel\Traits;

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Contract\Auth\Guard as GuardContract;
use Auth0\Laravel\Entities\Credential;
use Auth0\Laravel\Model\Imposter;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * @deprecated 7.5.0 Use of this trait is longer recommended. Use the Impersonate trait instead. This trait will be removed in the next major release.
 */
trait ActingAsAuth0User
{
    public array $defaultActingAsAttributes = [
        'sub'   => 'some-auth0-user-id',
        'azp'   => 'some-auth0-application-client-id',
        'scope' => '',
    ];

    /**
     * Set the currently logged in user for the application.
     *
     * @param array<mixed> $attributes The attributes to use for the user.
     * @param null|string  $guard      The guard to impersonate with.
     *
     * @return $this The current test case instance.
     */
    public function actingAsAuth0User(
        array $attributes = [],
        ?string $guard = 'auth0',
    ) {
        $issued     = time();
        $expires    = $issued + 60 * 60;
        $timestamps = ['iat' => $issued, 'exp' => $expires];
        $attributes = array_merge($this->defaultActingAsAttributes, $timestamps, $attributes);

        $instance = auth()->guard($guard);
        $user     = new Imposter($attributes);

        if ($instance instanceof GuardContract) {
            $credential = Credential::create(
                user: $user,
                accessTokenScope: $attributes['scope'] ? explode(' ', $attributes['scope']) : [],
            );

            $instance->setCredential($credential, Guard::SOURCE_IMPERSONATE);
            $instance->setImpersonating(true);
        }

        return $this->actingAs($user, $guard);
    }

    abstract public function actingAs(Authenticatable $user, $guard = null);
}
