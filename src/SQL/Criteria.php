<?php

namespace Nutrition\SQL;

class Criteria
{
    /** @var boolean */
    private $qmMode;

    /** @var array */
    private $criteria = [''];


    public function __construct($qmMode = false)
    {
        $this->qmMode = $qmMode;
    }

    /**
     * Create static class
     * @return static
     */
    public static function create($qmMode = false)
    {
        return new static($qmMode);
    }

    /**
     * Get criteria
     * @return array
     */
    public function get()
    {
        return $this->criteria[0] ? $this->criteria : null;
    }

    /**
     * Add criteria
     * @param string $criteria
     * @param mixed $value
     * @param string $before
     */
    public function addCriteria($criteria, $value = null, $before = 'and')
    {
        if ($value && is_array($value)) {
            $criteriaX = $this->buildCriteria($value, $this->qmMode);
            array_shift($criteriaX);
            $this->criteria = array_merge($this->criteria, $criteriaX);
        }

        $this->criteria[0] .= ($this->criteria[0] ? " $before " : '') . $criteria;

        return $this;
    }

    /**
     * Add key
     * @param string $key
     * @param mixed $value
     * @param string $opr
     * @param string $before and or etc
     */
    public function add($key, $value = null, $opr = null, $before = 'and')
    {
        $sValue = $value;
        if ($value) {
            if (is_array($value)) {
                $criteria = $this->buildCriteria($value, $this->qmMode);
                $sValue = '('.array_shift($criteria).')';
                $this->criteria = array_merge($this->criteria, $criteria);
                $opr = $opr ?: 'in';
            } else {
                $opr = $opr ?: '=';
                if ($this->qmMode) {
                    $sValue = '?';
                    $this->criteria[] = $value;
                } else {
                    $sKey = ":$key";
                    $sValue = $sKey;
                    $this->criteria[$sKey] = $value;
                }
            }
        }

        $this->criteria[0] .= ($this->criteria[0] ? " $before " : '') . $key .
            ($opr ? " $opr " : '') . $sValue;

        return $this;
    }

    /**
     * Array to mapper criteria
     * @param  array   $values
     * @param  boolean $qmMode force question mark placeholder
     * @return string
     */
    public static function buildCriteria(array $values, $qmMode = false)
    {
        reset($values);
        $qmMode = $qmMode ?: is_numeric(key($values));

        if ($qmMode) {
            $result = array_values($values);
            array_unshift($result, str_repeat('?,', count($values)-1).'?');

            return $result;
        }

        $result = [''];
        foreach ($values as $key => $value) {
            $key = ":$key";
            $result[0] .= ($result[0]?',':'').$key;
            $result[$key] = $value;
        }

        return $result;
    }
}
