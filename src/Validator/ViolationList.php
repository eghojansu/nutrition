<?php

namespace Nutrition\Validator;

use Magic;

/**
 * Violation container
 */
class ViolationList extends Magic
{
    private $violations = [];

    public function &get($key)
    {
        return array_key_exists($key, $this->violations) ?
            $this->violations[$key] : [];
    }

    public function set($key, $value)
    {
        $this->violations[$key] = (array) $value;

        return $this;
    }

    public function exists($key)
    {
        return array_key_exists($key, $this->violations) && count($this->violations[$key]) > 0;
    }

    public function clear($key)
    {
        unset($this->violations[$key]);
    }

    /**
     * Add violation
     * @param string $key
     * @param array|string $message
     */
    public function add($key, $message)
    {
        if (empty($this->violations[$key])) {
            $this->violations[$key] = [];
        }

        $this->violations[$key] = array_merge(
            $this->violations[$key],
            (array) $message
        );

        return $this;
    }

    /**
     * Get all violation
     * @return array
     */
    public function all()
    {
        return $this->violations;
    }

    /**
     * Has violation
     * @return boolean
     */
    public function hasViolation()
    {
        return count($this->violations) > 0;
    }

    /**
     * Has violation complement
     * @return boolean
     */
    public function hasNoViolation()
    {
        return count($this->violations) === 0;
    }
}
