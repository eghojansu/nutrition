<?php

namespace Nutrition\Validator\Constraint;

class PhoneNumber extends AbstractConstraint
{
    public function __construct(array $option = [])
    {
        parent::__construct($option);

        $this->option += [
            'format' => '/^(\+\d{1,2})?(\d{7,14})$/',
        ];
    }

    /**
     * {@inheritdoc}
    */
    public function validate()
    {
        if (null !== $this->value) {
            $this->valid = (bool) preg_match($this->option['format'], $this->value);
        }

        return $this;
    }
}
