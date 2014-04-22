<?php namespace Auth0\Login;

use Config;
use Auth0SDK\Auth0;

/**
 * Service that provides access to the Auth0 SDK.
 */
class Auth0Service {
    /**
     * Constructor, creates an instance of the Auth0 SDK using
     * the config set in the laravel way and using a LaravelSession
     * as a store mechanism
     */
    function __construct() {
        $auth0Config = Config::get('auth0::config');
        $auth0Config['store'] = new LaravelSessionStore();
        $this->auth0 = new Auth0($auth0Config);
    }

    /**
     * Logouts the user from the SDK
     */
    public function logout() {
        $this->auth0->logout();
    }

    /**
     * If the user is logged in, returns the user information
     * @return \Auth0\Login\Auth0User User info as described in https://docs.auth0.com/user-profile
     */
    public function getUserInfo() {
        // Get the user info from auth0
        $userInfo = $this->auth0->getUserInfo();

        $auth0User = new Auth0User($userInfo);
        return $auth0User;
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


}
