<?php

namespace Nutrition\Validator\Constraint;

class Between extends AbstractConstraint
{
    const MESSAGE_DEFAULT = 'Nilai ini harus diantara {max} dan {min}.';


    public function __construct(array $option = [])
    {
        parent::__construct($option);

        $this->option += [
            'max'=>null,
            'min'=>null,
        ];
    }

    /**
     * {@inheritdoc}
    */
    public function validate()
    {
        if (null !== $this->value) {
            $this->valid = $this->value >= $this->option['min'] &&
                           $this->value <= $this->option['max'];
        }

        return $this;
    }
}
