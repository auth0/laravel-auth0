<?php namespace Auth0\Login;

use Config;
use Auth0SDK\Auth0;

class Auth0Service {
    function __construct() {
        $auth0Config = Config::get('auth0::config');
        $auth0Config['store'] = new LaravelSessionStore();
        $this->auth0 = new Auth0($auth0Config);
    }

    public function logout() {
        $this->auth0->logout();
    }

    public function getUserInfo() {
        // Get the user info from auth0
        $userInfo = $this->auth0->getUserInfo();

        $auth0User = new Auth0User($userInfo);
        return $auth0User;
    }

    private $_onLoginCb = null;
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
