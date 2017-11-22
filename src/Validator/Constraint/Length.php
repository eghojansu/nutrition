<?php

namespace Nutrition\Validator\Constraint;

class Length extends AbstractConstraint
{
    const MESSAGE_MAX = 'Nilai ini terlalu panjang. Panjang maksimal {max} karakter.';
    const MESSAGE_MIN = 'Nilai ini terlalu pendek. Panjang minimal {min} karakter.';

    private $maxInvalid = false;
    private $minInvalid = false;

    public function __construct(array $option = [])
    {
        $this->option = $option + [
            'max' => 255,
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
            $len = strlen($this->value);

            $this->maxInvalid = $len > $this->option['max'];
            $this->minInvalid = $len < $this->option['min'];
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
