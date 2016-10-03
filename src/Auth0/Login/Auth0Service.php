<?php namespace Auth0\Login;

use Config;
use Auth0\SDK\API\Authentication;
use Auth0\SDK\JWTVerifier;
use Illuminate\Contracts\Container\BindingResolutionException;

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
    private function getSDK() 
    {
        if (is_null($this->auth0)) 
        {
            $auth0Config = config('laravel-auth0');

            $auth0Config['store'] = new LaravelSessionStore();

            $auth0 = new Authentication($auth0Config['domain'], $auth0Config['client_id']);

            $this->auth0 = $auth0->get_oauth_client($auth0Config['client_secret'], $auth0Config['redirect_uri']);
        }
        return $this->auth0;

    }
    /**
     * Logs the user out from the SDK.
     */
    public function logout() {
        $this->getSDK()->logout();
    }

    /**
     * If the user is logged in, returns the user information
     * 
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
    
    private $rememberUser = false;
    /**
     * Use this to either enable or disable the "remember" function for users
     *
     * @param null $value
     * @return bool|null
     */
    public function rememberUser($value = null) {
        if($value !== null){
            $this->rememberUser = $value;
        }

        return $this->rememberUser;
    }

    private $apiuser;
    public function decodeJWT($encUser) 
    {
        try {
            $cache = \App::make('\Auth0\SDK\Helpers\Cache\CacheHandler');
        } catch(BindingResolutionException $e) {
            $cache = null;
        }

        $verifier = new JWTVerifier([
            'valid_audiences' => [config('laravel-auth0.client_id'), config('laravel-auth0.api_identifier')],
            'client_secret' => config('laravel-auth0.client_secret'),
            'authorized_iss' => config('laravel-auth0.authorized_issuers'),
            'cache' => $cache,
        ]);

        $this->apiuser = $verifier->verifyAndDecode($encUser);
        
        return $this->apiuser;
    }


    public function jwtuser() {
        return $this->apiuser;
    }
}
