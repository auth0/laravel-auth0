<?php

declare(strict_types=1);

namespace Auth0\Laravel\Traits;

use Auth0\Laravel\Entities\CredentialEntity;
use Auth0\Laravel\Guards\GuardContract;
use Auth0\Laravel\UserProvider;
use Auth0\Laravel\Users\ImposterUser;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Set the currently logged in user for the application. Only intended for unit testing.
 *
 * @deprecated 7.8.0 Use Auth0\Laravel\Traits\Impersonate instead.
 *
 * @api
 */
trait ActingAsAuth0User
{
    public array $defaultActingAsAttributes = [
        'sub' => 'some-auth0-user-id',
        'azp' => 'some-auth0-application-client-id',
        'scope' => '',
    ];

    /**
     * Set the currently logged in user for the application. Only intended for unit testing.
     *
     * @param array<mixed> $attributes The attributes to use for the user.
     * @param null|string  $guard      The guard to impersonate with.
     * @param ?int         $source
     */
    public function actingAsAuth0User(
        array $attributes = [],
        ?string $guard = null,
        ?int $source = GuardContract::SOURCE_TOKEN,
    ): self {
        $issued = time();
        $expires = $issued + 60 * 60;
        $timestamps = ['iat' => $issued, 'exp' => $expires];
        $attributes = array_merge($this->defaultActingAsAttributes, $timestamps, $attributes);
        $scope = $attributes['scope'] ? explode(' ', $attributes['scope']) : [];
        unset($attributes['scope']);

        $instance = auth()->guard($guard);

        if (! $instance instanceof GuardContract) {
            $user = new ImposterUser($attributes);

            return $this->actingAs($user, $guard);
        }

        $provider = new UserProvider();

        if (GuardContract::SOURCE_SESSION === $source) {
            $user = $provider->getRepository()->fromSession($attributes);
        } else {
            $user = $provider->getRepository()->fromAccessToken($attributes);
        }

        $credential = CredentialEntity::create(
            user: $user,
            accessTokenScope: $scope,
        );

        $instance->setImpersonating($credential, $source);

        return $this->actingAs($user, $guard);
    }

    abstract public function actingAs(Authenticatable $user, $guard = null);
}
