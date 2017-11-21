<?php

namespace Nutrition\Validator\Constraint;

use InvalidArgumentException;

class Regex extends AbstractConstraint
{
    public function __construct(array $option = [])
    {
        parent::__construct($option);

        $this->option += [
            'pattern' => null,
        ];
    }

    /**
     * {@inheritdoc}
    */
    public function validate()
    {
        if (null === $this->option['pattern']) {
            throw new InvalidArgumentException('Pattern should be supplided');
        }
        if (null !== $this->value) {
            $this->valid = (bool) preg_match($this->option['pattern'], $this->value);
        }

        return $this;
    }
}
