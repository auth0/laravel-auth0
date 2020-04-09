<?php

namespace Auth0\Login;

use Auth0\SDK\Auth0;
use Auth0\SDK\Store\StoreInterface;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Http\RedirectResponse;
use Psr\SimpleCache\CacheInterface;

/**
 * Service that provides access to the Auth0 SDK.
 */
class Auth0Service
{
    /**
     * @var Auth0
     */
    private $auth0;

    private $apiuser;
    private $_onLoginCb = null;
    private $rememberUser = false;

    /**
     * Auth0Service constructor.
     *
     * @param array|null $auth0Config
     * @param StoreInterface|null $store
     * @param CacheInterface|null $cache
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __construct(
        array $auth0Config,
        StoreInterface $store = null,
        CacheInterface $cache = null
    )
    {

        if (!$auth0Config instanceof ConfigRepository && !is_array($auth0Config)) {
            $auth0Config = config('laravel-auth0');
        }

        $store = $auth0Config['store'] ?? $store;
        if (false !== $store && !$store instanceof StoreInterface) {
            $store = new LaravelSessionStore();
        }
        $auth0Config['store'] = $store;

        $cache = $auth0Config['cache_handler'] ?? $cache;
        if (!($cache instanceof CacheInterface)) {
            $cache = app()->make('cache.store');
        }
        $auth0Config['cache_handler'] = $cache;

        $this->auth0 = new Auth0($auth0Config);
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
    public function login($connection = null, $state = null, $additional_params = ['scope' => 'openid profile email'], $response_type = 'code')
    {
        if ($connection && empty( $additional_params['connection'] )) {
            $additional_params['connection'] = $connection;
        }

        if ($state && empty( $additional_params['state'] )) {
            $additional_params['state'] = $state;
        }

        $additional_params['response_type'] = $response_type;
        $auth_url = $this->auth0->getLoginUrl($additional_params);
        return new RedirectResponse($auth_url);
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
     * @param array $verifierOptions
     *
     * @return array
     * @throws \Auth0\SDK\Exception\InvalidTokenException
     */
    public function decodeJWT($encUser, array $verifierOptions = [])
    {
        $this->apiuser = $this->auth0->decodeIdToken($encUser, $verifierOptions);
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

    public function getRefreshToken()
    {
        return $this->getSDK()->getRefreshToken();
    }

    public function jwtuser()
    {
        return $this->apiuser;
    }
}
