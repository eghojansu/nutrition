<?php

namespace Nutrition;

use Base;
use Prefab;
use DB\Cursor;

class User extends Prefab
{
    /**
     * @var Nutrition\UserProviderInterface
     */
    protected $provider;

    /**
     * @var string
     */
    protected $sessionKey;

    /**
     * Construct User
     */
    public function __construct()
    {
        $base = Base::instance();
        $provider = $base->get('SECURITY.provider');
        $this->sessionKey = 'SESSION.'.($base->get('SECURITY.sessionKey')?:'user');
        if ($provider && ($interfaces = class_implements($provider)) && in_array(UserProviderInterface::class, $interfaces)) {
            $this->provider = is_object($provider)?$provider:(new $provider);
            $this->loadFromSession();
        }
        elseif ($provider) {
            user_error('User provider must implements '.UserProviderInterface::class);
        }
    }

    /**
     * Load user data from session
     *
     * @return Object $this
     */
    public function loadFromSession()
    {
        $values = Base::instance()->get($this->sessionKey);
        $this->provider->copyfrom($values?:[]);

        return $this;
    }

    /**
     * Authenticate user
     *
     * @param  string
     * @param  string
     * @return boolean
     */
    public function authenticate($value, $password)
    {
        if ($this->provider->loadUser($value)->valid() && $this->provider->validatePassword($password)) {
            Base::instance()->set($this->sessionKey, $this->provider->cast());

            return true;
        }

        return false;
    }

    /**
     * Check user was logged
     *
     * @return boolean
     */
    public function wasLogged()
    {
        return false === empty(Base::instance()->get($this->sessionKey));
    }

    /**
     * @return Object $this
     */
    public function logout()
    {
        Base::instance()->clear($this->sessionKey);

        return $this;
    }

    public function __call($method, array $args)
    {
        if (method_exists($this->provider, $method)) {
            return call_user_func_array([$this->provider, $method], $args);
        }

        user_error(sef::class.'::'.$method.' was not exists');
    }
}
