<?php

namespace Nutrition\Validator;

use Closure;
use InvalidArgumentException;

class Validation
{
    private $constraints;
    private $originalData;
    private $data = [];
    private $after;

    public function __construct(array $data, array $constraints = [])
    {
        $this->originalData = $data;
        $this->constraints = $constraints;
    }

    /**
     * Create static
     * @param  array  $data
     * @param  array  $constraints
     * @return static
     */
    public static function create(array $data, array $constraints = [])
    {
        return new static($data, $constraints);
    }

    /**
     * Add constraint
     * @param string                         $key
     * @param Constraint\ConstraintInterface $constraint
     */
    public function add($key, Constraint\ConstraintInterface $constraint)
    {
        if (empty($this->constraints[$key])) {
            $this->constraints[$key] = [];
        }
        $this->constraints[$key][] = $constraint;

        return $this;
    }

    /**
     * Validate
     * @param array $groups
     * @return ViolationList
     */
    public function validate(array $groups = ['Default'])
    {
        $groups = (array) $groups;
        $violations = new ViolationList();
        foreach ($this->constraints as $key => $constraints) {
            $value = array_key_exists($key, $this->originalData) ? $this->originalData[$key] : null;
            $constraints = is_array($constraints) ? $constraints : [$constraints];
            foreach ($constraints as $constraint) {
                if ($this->useConstraint($groups, $constraint)) {
                    if ($constraint->setValue($value)->validate()->isValid()) {
                        $this->data[$key] = $constraint->getValue();
                    } else {
                        $violations->add($key, $constraint->getMessages());
                    }
                }
            }
        }

        if (null !== $this->after) {
            $this->data = call_user_func_array($this->after, [$this->data, $violations]);
        }

        return $violations;
    }

    /**
     * Call after validation
     * @param  mixed $callable
     * @return  $this
     */
    public function after($callable)
    {
        if (!is_callable($callable)) {
            throw new InvalidArgumentException('Argument should be callable');
        }

        $this->after = $callable;

        return $this;
    }

    /**
     * Get constraints
     * @return array
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * Get data
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Should we use this constraint (check agains groups)
     * @param  array               $groups
     * @param  ConstraintInterface $constraint
     * @return boolean
     */
    protected function useConstraint(array $groups, Constraint\ConstraintInterface $constraint)
    {
        return count(array_intersect($constraint->getGroups(), $groups)) > 0;
    }
}
