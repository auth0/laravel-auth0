<?php namespace Auth0\Login;

use Config;
use Auth0\SDK\Auth0;
use Auth0\SDK\Auth0JWT;

/**
 * Service that provides access to the Auth0 SDK.
 */
class Auth0Service {
    private $auth0;

    /**
     * Creates an instance of the Auth0 SDK using
     * the config set in the laravel way and using a LaravelSession
     * as a store mechanism
     */
    private function getSDK() {
        if (is_null($this->auth0)) {
            $auth0Config = config('laravel-auth0');

            $auth0Config['store'] = new LaravelSessionStore();
            $this->auth0 = new Auth0($auth0Config);
        }
        return $this->auth0;

    }
    /**
     * Logouts the user from the SDK
     */
    public function logout() {
        $this->getSDK()->logout();
    }

    /**
     * If the user is logged in, returns the user information
     * @return array with the User info as described in https://docs.auth0.com/user-profile and the user access token
     */
    public function getUser() {
        // Get the user info from auth0
        $auth0 = $this->getSDK();
        $user = $auth0->getUser();

        if ($user === null) return null;

        return [
            'profile' => $user,
            'accessToken' => $auth0->getAccessToken()
        ];
    }

    private $_onLoginCb = null;
    /**
     * Sets a callback to be called when the user is logged in
     * @param  callback $cb A function that receives an auth0User and receives a Laravel user
     */
    public function onLogin($cb) {
        $this->_onLoginCb = $cb;
    }

    public function hasOnLogin () {
        return $this->_onLoginCb !== null;
    }

    public function callOnLogin($auth0User) {
        return call_user_func($this->_onLoginCb, $auth0User);
    }

    private $apiuser;
    public function decodeJWT($encUser) {

        $client_id = config('laravel-auth0.client_id');
        $client_secret = config('laravel-auth0.client_secret');

        $this->apiuser = Auth0JWT::decode($encUser, $client_id, $client_secret);

        return $this->apiuser;
    }

    public function jwtuser() {
        return $this->apiuser;
    }
}
