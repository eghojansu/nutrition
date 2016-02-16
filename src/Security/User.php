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
    public $data;
    /**
     * Password encoder
     * @var  PasswordEncoderInterface
     */
    public $encoder;

    public function __construct()
    {
        $app = Base::instance();
        $provider = $app->get('SECURITY.provider');
        $encoder  = $app->get('SECURITY.encoder');
        $provider = new $provider;
        $encoder  = new $encoder;
        if (!($provider instanceof UserProviderInterface)) {
            throw new InvalidConfigurationException('User provider must implements Nutrition\\Security\\UserProviderInterface');
        }
        if (!($encoder instanceof PasswordEncoderInterface)) {
            throw new InvalidConfigurationException('Password encoder must implements Nutrition\\Security\\PasswordEncoderInterface');
        }
        $this->data    = $provider;
        $this->encoder = $encoder;

        $user_id = $this->getSession('id');
        $this->data->loadUserData($user_id);
    }

    public function verify($password)
    {
        return $this->encoder->verify($password, $this->data->getPassword());
    }

    public function authenticate($username, $password)
    {
        if ($this->data->authenticate($username) && $this->verify($password)) {
            $this->updateSession();

            return true;
        }

        return false;
    }

    public function updateSession()
    {
        $this->setSession($this->data->getId());
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
        Base::instance()->clear('SESSION.'.$this->data->getSessionID());
    }

    protected function getSession($key = null)
    {
        $session = Base::instance()->get('SESSION.'.$this->data->getSessionID());

        return $key?(isset($session[$key])?$session[$key]:null):$session;
    }

    protected function setSession($id)
    {
        Base::instance()->set('SESSION.'.$this->data->getSessionID(), [
            'id' => $id,
            ]);
    }
}