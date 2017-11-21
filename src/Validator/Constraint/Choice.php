<?php

namespace Nutrition\Validator\Constraint;

class Choice extends AbstractConstraint
{
    const MESSAGE_DEFAULT = 'Nilai ini tidak valid. Pilihan yang valid adalah {choices}.';

    public function __construct(array $option = [])
    {
        parent::__construct($option);

        $this->option += [
            'choices' => [],
            'multiple' => false,
        ];
    }

    /**
     * {@inheritdoc}
    */
    public function validate()
    {
        if (null !== $this->value) {
            if ($this->option['multiple']) {
                $value = (array) $this->value;
                $this->valid = count(array_intersect($value, $this->option['choices'])) > 0;
            } else {
                $this->valid = is_array($this->value) ? false :
                    in_array($this->value, $this->option['choices']);
            }
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

        return str_replace(
            '{choices}',
            '"'.implode('", "', $this->option['max']).'"',
            $this->option['message']
        );
    }
}
