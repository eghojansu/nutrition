<?php

namespace Nutrition\Validator\Constraint;

class Email extends AbstractConstraint
{
    const MESSAGE_DEFAULT = 'Nilai ini bukan email yang valid.';


    /**
     * {@inheritdoc}
    */
    public function validate()
    {
        if (null !== $this->value) {
            $this->value = filter_var($this->value, FILTER_VALIDATE_EMAIL);
            $this->valid = false !== $this->value;
        }

        return $this;
    }
}
