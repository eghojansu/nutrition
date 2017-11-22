<?php

namespace Nutrition\Validator\Constraint;

abstract class AbstractConstraint implements ConstraintInterface
{
    const MESSAGE_DEFAULT = 'Nilai ini tidak valid';

    /** @var boolean */
    protected $valid = true;

    /** @var mixed */
    protected $value;

    /** @var array */
    protected $option;


    /**
     * Class constructor
     * @param array $option
     */
    public function __construct(array $option = [])
    {
        $this->option = $option + [
            'message' => static::MESSAGE_DEFAULT,
            'groups' => ['Default'],
            'trim' => true,
            'normalizer' => null,
        ];
    }

    /**
     * {@inheritdoc}
    */
    public function getMessages()
    {
        if ($this->isValid()) {
            return null;
        }

        return $this->option['message'];
    }

    /**
     * {@inheritdoc}
    */
    public function getGroups()
    {
        return $this->option['groups'];
    }

    /**
     * {@inheritdoc}
    */
    public function setValue($value)
    {
        if ($this->option['trim'] && is_string($value)) {
            $value = trim($value);
        }
        if ($this->option['normalizer']) {
            $value = call_user_func_array($this->option['normalizer'], [$value]);
        }
        $this->value = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
    */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
    */
    public function isValid()
    {
        return $this->valid;
    }

    /**
     * {@inheritdoc}
    */
    abstract public function validate();
}
