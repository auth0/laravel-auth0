<?php namespace Auth0\Login;

use \Illuminate\Routing\Controller;

class Auth0Controller extends Controller {


    public function callback() {
        $service = \App::make('auth0');
        $auth0User = $service->getUserInfo();
        if ($auth0User) {
            if ($service->hasOnLogin()) {
                $user = $service->callOnLogin($auth0User);
            } else {
                $user = $auth0User;
            }
            \Auth::login($user);
        }
        return  \Redirect::intended('/');
    }

}