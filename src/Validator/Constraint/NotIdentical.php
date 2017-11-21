<?php

namespace Nutrition\Validator\Constraint;

class NotIdentical extends AbstractConstraint
{
    const MESSAGE_DEFAULT = 'Nilai ini harus tidak identik dengan {value}.';


    public function __construct(array $option = [])
    {
        parent::__construct($option);

        $this->option += [
            'value'=>null,
        ];
    }

    /**
     * {@inheritdoc}
    */
    public function validate()
    {
        if (null !== $this->value) {
            $this->valid = $this->value !== $this->option['value'];
        }

        return $this;
    }
}
