<?php

namespace Nutrition\Validator\Constraint;

class Boolean extends AbstractConstraint
{
    /**
     * {@inheritdoc}
    */
    public function validate()
    {
        if (null !== $this->value) {
            $this->value = filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
            $this->valid = false !== $this->value;
        }

        return $this;
    }
}
