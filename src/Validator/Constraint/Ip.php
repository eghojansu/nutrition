<?php

namespace Nutrition\Validator\Constraint;

class Ip extends AbstractConstraint
{
    const MESSAGE_DEFAULT = 'Nilai ini bukan alamat IP yang valid.';


    /**
     * {@inheritdoc}
    */
    public function validate()
    {
        if (null !== $this->value) {
            $this->value = filter_var($this->value, FILTER_VALIDATE_IP);
            $this->valid = false !== $this->value;
        }

        return $this;
    }
}
