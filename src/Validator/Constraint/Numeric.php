<?php

namespace Nutrition\Validator\Constraint;

class Numeric extends AbstractConstraint
{
    const MESSAGE_MAX = 'Nilai ini terlalu panjang. Nilai maksimal {max}.';
    const MESSAGE_MIN = 'Nilai ini terlalu pendek. Nilai minimal {min}.';

    private $maxInvalid = false;
    private $minInvalid = false;

    public function __construct(array $option = [])
    {
        $this->option = $option + [
            'max' => 1000,
            'min' => 0,
            'max_message' => static::MESSAGE_MAX,
            'min_message' => static::MESSAGE_MIN,
            'groups' => ['Default']
        ];
    }

    /**
     * {@inheritdoc}
    */
    public function validate()
    {
        if (null !== $this->value) {
            $this->maxInvalid = $this->value > $this->option['max'];
            $this->minInvalid = $this->value < $this->option['min'];
        }

        return $this;
    }

    /**
     * {@inheritdoc}
    */
    public function getMessages()
    {
        if ($this->isValid()) {
            return null;
        }

        $messages = [];
        if ($this->maxInvalid) {
            $messages[] = str_replace('{max}', $this->option['max'], $this->option['max_message']);
        }
        if ($this->minInvalid) {
            $messages[] = str_replace('{min}', $this->option['min'], $this->option['min_message']);
        }

        return $messages;
    }

    /**
     * {@inheritdoc}
    */
    public function isValid()
    {
        return !($this->maxInvalid || $this->minInvalid);
    }
}
