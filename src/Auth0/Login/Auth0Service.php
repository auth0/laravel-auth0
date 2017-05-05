<?php

namespace Auth0\Login;

use Config;
use Auth0\SDK\API\Authentication;
use Auth0\SDK\Auth0;
use Auth0\SDK\JWTVerifier;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Service that provides access to the Auth0 SDK.
 */
class Auth0Service
{
    private $auth0Config;
    private $auth0;
    private $authApi;
    private $apiuser;
    private $_onLoginCb = null;
    private $rememberUser = false;

    public function __construct() {
      $this->auth0Config = config('laravel-auth0');

      $this->auth0Config['store'] = new LaravelSessionStore();

      $this->authApi = new Authentication($this->auth0Config['domain'], $this->auth0Config['client_id']);

      $this->auth0 = new Auth0($this->auth0Config);
    }

    /**
     * Creates an instance of the Auth0 SDK using
     * the config set in the laravel way and using a LaravelSession
     * as a store mechanism.
     */
    private function getSDK()
    {
        return $this->auth0;
    }

    /**
     * Logs the user out from the SDK.
     */
    public function logout()
    {
        $this->getSDK()->logout();
    }

    /**
     * Redirects the user to the hosted login page
     */
    public function login($connection = null, $state = null, $aditional_params = [], $response_type = 'code')
    {
      $url = $this->authApi->get_authorize_link($response_type, $this->auth0Config['redirect_uri'], $connection, $state, $aditional_params);
      return \Redirect::to($url);
    }

    /**
     * If the user is logged in, returns the user information.
     *
     * @return array with the User info as described in https://docs.auth0.com/user-profile and the user access token
     */
    public function getUser()
    {
        // Get the user info from auth0
        $auth0 = $this->getSDK();
        $user = $auth0->getUser();

        if ($user === null) {
            return;
        }

        return [
            'profile' => $user,
            'accessToken' => $auth0->getAccessToken(),
        ];
    }

    /**
     * Sets a callback to be called when the user is logged in.
     *
     * @param callback $cb A function that receives an auth0User and receives a Laravel user
     */
    public function onLogin($cb)
    {
        $this->_onLoginCb = $cb;
    }

    /**
     * @return bool
     */
    public function hasOnLogin()
    {
        return $this->_onLoginCb !== null;
    }

    /**
     * @param $auth0User
     *
     * @return mixed
     */
    public function callOnLogin($auth0User)
    {
        return call_user_func($this->_onLoginCb, $auth0User);
    }

    /**
     * Use this to either enable or disable the "remember" function for users.
     *
     * @param null $value
     *
     * @return bool|null
     */
    public function rememberUser($value = null)
    {
        if ($value !== null) {
            $this->rememberUser = $value;
        }

        return $this->rememberUser;
    }

    /**
     * @param $encUser
     *
     * @return mixed
     */
    public function decodeJWT($encUser)
    {
        try {
            $cache = \App::make('\Auth0\SDK\Helpers\Cache\CacheHandler');
        } catch (BindingResolutionException $e) {
            $cache = null;
        }

        $secret_base64_encoded = config('laravel-auth0.secret_base64_encoded');

        if (is_null($secret_base64_encoded)) {
          $secret_base64_encoded = true;
        }

        $verifier = new JWTVerifier([
            'suported_algs' => ['HS256', 'RS256'],
            'valid_audiences' => [config('laravel-auth0.client_id'), config('laravel-auth0.api_identifier')],
            'supported_algs' => config('laravel-auth0.supported_algs', ['HS256']),
            'client_secret' => config('laravel-auth0.client_secret'),
            'authorized_iss' => config('laravel-auth0.authorized_issuers'),
            'secret_base64_encoded' => $secret_base64_encoded,
            'cache' => $cache,
        ]);

        $this->apiuser = $verifier->verifyAndDecode($encUser);

        return $this->apiuser;
    }

    public function getIdToken()
    {
        return $this->getSDK()->getIdToken();
    }

    public function getAccessToken()
    {
        return $this->getSDK()->getAccessToken();
    }

    public function jwtuser()
    {
        return $this->apiuser;
    }
}
