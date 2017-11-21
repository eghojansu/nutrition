<?php

namespace Nutrition\Validator\Constraint;

class Url extends AbstractConstraint
{
    const MESSAGE_DEFAULT = 'Nilai ini bukan URL yang valid.';


    /**
     * {@inheritdoc}
    */
    public function validate()
    {
        if (null !== $this->value) {
            $this->value = filter_var($this->value, FILTER_VALIDATE_URL);
            $this->valid = false !== $this->value;
        }

        return $this;
    }
}
