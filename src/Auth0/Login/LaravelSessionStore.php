<?php

namespace Auth0\Login;

use Session;
use Auth0\SDK\Store\StoreInterface;

class LaravelSessionStore implements StoreInterface
{
    const BASE_NAME = 'auth0_';

    /**
     * Persists $value on $_SESSION, identified by $key.
     *
     * @see Auth0SDK\BaseAuth0
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        $key_name = $this->getSessionKeyName($key);
        Session::put($key_name, $value);
    }

    /**
     * @param $key
     * @param null $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $key_name = $this->getSessionKeyName($key);

        return Session::get($key_name, $default);
    }

    /**
     * Removes a persisted value identified by $key.
     *
     * @see Auth0SDK\BaseAuth0
     *
     * @param string $key
     */
    public function delete($key)
    {
        $key_name = $this->getSessionKeyName($key);

        Session::forget($key_name);
    }

    /**
     * Constructs a session var name.
     *
     * @param string $key
     *
     * @return string
     */
    public function getSessionKeyName($key)
    {
        return self::BASE_NAME.'_'.$key;
    }
}
