<?php

namespace Nutrition\Security;

use Base;
use Nutrition\InvalidConfigurationException;

class User
{
    /**
     * User data
     * @var UserProviderInterface
     */
    public $provider;
    /**
     * Password encoder
     * @var  PasswordEncoderInterface
     */
    public $encoder;

    public function __construct()
    {
        $this->prepareConfig('provider', 'Nutrition\\Security\\UserProviderInterface');
        $this->prepareConfig('encoder', 'Nutrition\\Security\\PasswordEncoderInterface');

        $user_id = $this->getSession('id');
        $this->provider->loadUserData($user_id);
    }

    public function verify($password)
    {
        return $this->encoder->verify($password, $this->provider->getPassword());
    }

    public function authenticate($username, $password)
    {
        if ($this->provider->authenticate($username) && $this->verify($password)) {
            $this->updateSession();

            return true;
        }

        return false;
    }

    public function updateSession()
    {
        $this->setSession($this->provider->getId());
    }

    public function isGuest()
    {
        return empty($this->getSession());
    }

    public function wasLogged()
    {
        return !$this->isGuest();
    }

    public function logout()
    {
        Base::instance()->clear('SESSION.'.$this->provider->getSessionID());
    }

    protected function getSession($key = null)
    {
        $session = Base::instance()->get('SESSION.'.$this->provider->getSessionID());

        return $key?(isset($session[$key])?$session[$key]:null):$session;
    }

    protected function setSession($id)
    {
        Base::instance()->set('SESSION.'.$this->provider->getSessionID(), [
            'id' => $id,
            ]);
    }

    protected function prepareConfig($name, $interface)
    {
        $app = Base::instance();
        $class = $app->get('SECURITY.'.$name);
        if (!$class || (($object = new $class) && !($object instanceof $interface))) {
            throw new InvalidConfigurationException('You need to supply SECURITY.'.$name.' class that implements '.$interface);
        }

        $this->{$name} = $object;
    }
}