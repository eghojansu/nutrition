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
     * Get filter
     * @return array
     */
    public function getFilter();

    /**
     * Set default filter status
     */
    public function setDefaultFilter($status);

    /**
     * Default filter status
     * @return boolean
     */
    public function getDefaultFilter();
}