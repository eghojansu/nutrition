<?php

namespace Nutrition\Validator\Constraint;

use InvalidArgumentException;

class Callback extends AbstractConstraint
{
    public function __construct(array $option = [])
    {
        parent::__construct($option);

        $this->option += [
            'callback' => null,
        ];
    }

    /**
     * {@inheritdoc}
    */
    public function validate()
    {
        if (null === $this->option['callback'] || !is_callable($this->option['callback'])) {
            throw new InvalidArgumentException('Constraint should be callable');
        }
        if (null !== $this->value) {
            $this->valid = (bool) call_user_func_array($this->option['callback'], [$this->value]);
        }

        return $this;
    }
}
