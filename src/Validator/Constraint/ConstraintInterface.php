<?php

namespace Nutrition\Validator\Constraint;

interface ConstraintInterface
{
    /**
     * Get violation message
     * @return array|string
     */
    public function getMessages();

    /**
     * Get groups
     * @return array
     */
    public function getGroups();

    /**
     * Set value to check
     * @param mixed $value
     * @return  $this
     */
    public function setValue($value);

    /**
     * Get value
     * @return  mixed
     */
    public function getValue();

    /**
     * Validate value
     * @return $this
     */
    public function validate();

    /**
     * Get constraint validity
     * @return boolean
     */
    public function isValid();
}
