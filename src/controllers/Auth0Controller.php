<?php

namespace Auth0\Login;

use Auth0\Login\Contract\Auth0UserRepository;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class Auth0Controller extends Controller
{
    /**
     * @var Auth0UserRepository
     */
    protected $userRepository;

    /**
     * Auth0Controller constructor.
     *
     * @param Auth0UserRepository $userRepository
     */
    public function __construct(Auth0UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Callback action that should be called by auth0, logs the user in.
     */
    public function callback()
    {
        // Get a handle of the Auth0 service (we don't know if it has an alias)
        $service = \App::make('auth0');

        // Try to get the user information
        $profile = $service->getUser();

        // Get the user related to the profile
        $auth0User = $this->userRepository->getUserByUserInfo($profile);

        if ($auth0User) {
            // If we have a user, we are going to log them in, but if
            // there is an onLogin defined we need to allow the Laravel developer
            // to implement the user as they want an also let them store it.
            $user = $auth0User;
            if ($service->hasOnLogin()) {
                $user = $service->callOnLogin($user);
            }

            Auth::login($user, $service->rememberUser());
        }

        return \Redirect::intended('/');
    }
}
