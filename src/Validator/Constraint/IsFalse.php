<?php

namespace Nutrition\Validator\Constraint;

class IsFalse extends AbstractConstraint
{
    const MESSAGE_DEFAULT = 'Nilai ini harus salah.';


    /**
     * {@inheritdoc}
    */
    public function validate()
    {
        if (null !== $this->value) {
            $this->value = filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
            $this->valid = $this->value === false;
        }

        return $this;
    }
}
