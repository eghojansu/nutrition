<?php

namespace Nutrition\Validator\Constraint;

use Nutrition\Security\Security;
use Nutrition\Security\UserManager;

class UserPassword extends AbstractConstraint
{
    const MESSAGE_DEFAULT = 'Nilai ini harus password user saat ini';

    /**
     * {@inheritdoc}
    */
    public function validate()
    {
        if (null !== $this->value) {
            $user = UserManager::instance()->getUser();

            if ($user) {
                $this->valid = Security::instance()->getPasswordEncoder()
                    ->verifyPassword($this->value, $user->getPassword());
            } else {
                $this->valid = false;
            }
        }

        return $this;
    }
}
