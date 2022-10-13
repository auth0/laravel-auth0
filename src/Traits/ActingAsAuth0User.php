<?php

declare(strict_types=1);

namespace Auth0\Laravel\Traits;

use Auth0\Laravel\Model\Stateless\User;
use Auth0\Laravel\StateInstance;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

trait ActingAsAuth0User
{
    abstract public function actingAs(UserContract $user, $guard = null);

    /**
     * use this method to impersonate a specific auth0 user
     * if you pass an attributes array, it will be merged with a set of default values.
     *
     * @return mixed
     */
    public function actingAsAuth0User(array $attributes = [])
    {
        $defaults = [
            'sub'   => 'some-auth0-user-id',
            'azp'   => 'some-auth0-appplication-client-id',
            'iat'   => time(),
            'exp'   => time() + 60 * 60,
            'scope' => '',
        ];

        $auth0user = new User(array_merge($defaults, $attributes));

        if ($auth0user->getAttribute('scope')) {
            app(StateInstance::class)->setAccessTokenScope(explode(' ', $auth0user->getAttribute('scope')));
        }

        return $this->actingAs($auth0user, 'auth0');
    }
}
