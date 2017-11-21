<?php

namespace Nutrition\Security;

use Base;
use Exception;
use InvalidArgumentException;
use Nutrition\Security\UserInterface;
use Prefab;

/**
 * Authentication util
 */
class Authentication extends Prefab
{
    /**
     * Authenticate with username and password
     * @param  string $username
     * @param  string $password
     * @return bool
     *
     * @throws Nutrition\Security\UsernameNotFoundException
     * @throws Nutrition\Security\InvalidPasswordException
     * @throws Nutrition\Security\ExpiredUserException
     * @throws Nutrition\Security\BlockedUserException
     * @throws InvalidArgumentException
     */
    public function attempt($username, $password)
    {
        $security = Security::instance();
        $user = $security->getUserProvider()->loadByUsername($username);

        if (!$user) {
            throw new UsernameNotFoundException();
        } elseif (!$user instanceof UserInterface) {
            throw new InvalidArgumentException(sprintf(
                '%s must be an instance of %s',
                get_class($user),
                UserInterface::class
            ));
        } elseif (!$password || !$security->getPasswordEncoder()->verifyPassword(
            $password,
            $user->getPassword()
        )) {
            throw new InvalidPasswordException();
        }

        $this->checkUser($user);

        UserManager::instance()->setUser($user);

        return true;
    }

    /**
     * Handle authentication attempt
     * @param  bool $executeNow should we execute now?
     * @param  string $username
     * @param  string $password
     * @param  string $toRoute    redirect to this route on success
     * @return string|null        error message
     */
    public function handleAttempt($executeNow, $username, $password, $toRoute)
    {
        if ($executeNow) {
            try {
                if ($this->attempt($username, $password)) {
                    Base::instance()->reroute($toRoute);
                }
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }

        return null;
    }

    /**
     * Check user instance
     * @param  Nutrition\Security\UserInterface $user
     *
     * @throws Nutrition\Security\ExpiredUserException
     * @throws Nutrition\Security\BlockedUserException
     */
    public function checkUser(UserInterface $user)
    {
        if ($user->isExpired()) {
            throw new ExpiredUserException();
        } elseif ($user->isBlocked()) {
            throw new BlockedUserException();
        }
    }
}
