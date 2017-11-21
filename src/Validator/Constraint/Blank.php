<?php

namespace Nutrition\Validator\Constraint;

class Blank extends AbstractConstraint
{
    const MESSAGE_DEFAULT = 'Nilai ini tidak boleh diisi.';


    /**
     * {@inheritdoc}
    */
    public function validate()
    {
        $this->valid = (bool) empty($this->value);

        return $this;
    }
}
