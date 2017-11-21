<?php

namespace Nutrition\Test\Fixture;

use Nutrition\Validator\Constraint\AbstractConstraint;

class CustomConstraint extends AbstractConstraint
{
    /**
     * {@inheritdoc}
    */
    public function validate()
    {
        $this->valid = $this->value === 'value';

        return $this;
    }
}
