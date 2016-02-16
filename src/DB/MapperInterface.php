<?php

namespace Nutrition\DB;

/**
 * Mapper interface
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

interface MapperInterface
{
    /**
     * Has error check
     * @return boolean
     */
    public function hasError();

    /**
     * Add error
     * @param  string $name
     * @param  string $message
     * @param  array  $args
     */
    public function addError($name, $message, $args = []);

    /**
     * Get error
     * @param  string $name
     * @return array
     */
    public function getError($name);

    /**
     * Get error as string with separator
     * @param  string $name
     * @param  string $separator
     * @return string
     */
    public function getErrorString($name, $separator = '<br>');

    /**
     * Get all error
     * @return array
     */
    public function getAllError();

    /**
     * Get error as string with separator
     * @param  string $separator
     * @param  string $separator2
     * @return string
     */
    public function getAllErrorString($separator = '<br>', $separator2 = '<br>');

    /**
     * Clear error
     */
    public function clearError();

    /**
     * Get rule
     * @return array
     */
    public function getRules();

    /**
     * Add rule
     * @param string $field
     * @param string $rule
     * @return  $this
     */
    public function addRule($field, $rule);

    /**
     * Check if rule was exists
     * @param  string $field
     * @param  string $rule
     * @return bool
     */
    public function ruleExists($field, $rule);

    /**
     * Set default validation status
     * @return  object $this
     */
    public function setDefaultValidation($status);

    /**
     * Default validation status
     * @return boolean
     */
    public function getDefaultValidation();

    /**
     * Validate this map
     * @param  string $mode
     * @return bool
     */
    public function validate($mode = 'default');
}