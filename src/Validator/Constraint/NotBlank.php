<?php

namespace Nutrition\Validator\Constraint;

class NotBlank extends AbstractConstraint
{
    const MESSAGE_DEFAULT = 'Nilai ini tidak boleh kosong.';


    /**
     * {@inheritdoc}
    */
    public function validate()
    {
        $this->valid = !empty($this->value);

        return $this;
    }
}
