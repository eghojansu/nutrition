<?php

namespace Nutrition\DB\SQL;

/**
 * DB\SQL\Mapper wrapper
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

use Base;
use DB\SQL;
use DB\SQL\Mapper;
use Registry;
use Nutrition\DB\MapperInterface;
use Nutrition\DB\Validation;

abstract class AbstractMapper extends Mapper implements MapperInterface
{
    /**
     * Default connection group
     * @var string
     */
    protected $defaultConnection = 'default';
    /**
     * Table name
     * @var string
     */
    protected $tableName;
    /**
     * Default fields to select
     * @var  string
     */
    protected $defaultField;
    /**
     * Time to live in cache
     * @var integer minutes
     */
    protected $ttl = 60;
    /**
     * Primary key(s)
     * @var string|array
     */
    protected $primaryKeys;
    /**
     * Enable/disable default filter
     * @var boolean
     */
    protected $defaultFilter = true;
    /**
     * Filters
     * @var array
     */
    protected $filters = [];
    /**
     * Field label
     * @var array
     */
    protected $labels = [];
    /**
     * Errors
     * @var array
     */
    protected $errors = [];

    /**
     * Save with filter
     * @param  string $mode
     * @return bool
     */
    public function safeSave($mode = 'default')
    {
        $validation = new Validation($this, $mode);
        if ($validation->validate()) {
            $this->save();

            return $this->valid();
        }

        return false;
    }

    /**
     * Generate new ID based on format
     * @param string $columName
     * @param string $format
     * @return string
     */
    public function generateID($columnName, $format)
    {
        $clone = clone $this;
        $clone->load(null, [
            'limit'=>1,
            'order'=>$columnName.' desc',
            ]);

        $last = 0;
        $boundPattern = '/\{([a-z0-9\- _\.]+)\}/i';
        if ($clone->valid()) {
            $pattern = preg_replace_callback($boundPattern, function($match) {
                return is_numeric($match[1])?
                    '(?<serial>'.str_replace('9', '[0-9]', $match[1]).')':
                    '(?<date>.{'.strlen(date($match[1])).'})';
            }, $format);
            if (preg_match('/^'.$pattern.'$/i', $clone[$columnName], $match))
                $last = $match['serial']*1;
        }

        return preg_replace_callback($boundPattern, function($match) use ($last) {
            return is_numeric($match[1])?
                str_pad($last+1, strlen($match[1]), '0', STR_PAD_LEFT):
                date($match[1]);
        }, $format);
    }

    /**
     * Get field label
     * @param  string $field
     * @return string
     */
    public function getLabel($field)
    {
        if (!isset($this->labels[$field])) {
            $this->labels[$field] = ucwords(implode(' ', array_filter(explode('_', Base::instance()->snakecase(lcfirst($field))))));
        }

        return $this->labels[$field];
    }

    /**
     * Get all labels
     * @return array
     */
    public function getLabels()
    {
        foreach ($this->fields as $field => $schema) {
            $this->getLabel($field);
        }

        return $this->labels;
    }

    /**
     * Has error check
     * @return boolean
     */
    public function hasError()
    {
        return count($this->errors) > 0;
    }

    /**
     * Add error
     * @param  string $name
     * @param  string $message
     * @param  array  $args
     */
    public function addError($name, $message, $args = [])
    {
        isset($this->errors[$name]) || $this->errors[$name] = [];
        $pattern = [
            '{field}'=>$name,
            '{label}'=>$this->getLabel($name),
            '{value}'=>$this->get($name)
        ];
        foreach ($args as $key => $value) {
            $pattern['{args_'.++$ctr.'}'] = is_array($value)?implode(', ', $value):$value;
        }
        $this->errors[$name][] = str_replace(array_keys($pattern), array_values($pattern), $message);
    }

    /**
     * Get error
     * @param  string $name
     * @return array
     */
    public function getError($name)
    {
        return isset($this->errors[$name])?$this->errors[$name]:[];
    }

    /**
     * Get error as string with separator
     * @param  string $name
     * @param  string $separator
     * @return string
     */
    public function getErrorString($name, $separator = '<br>')
    {
        return implode($separator, isset($this->errors[$name])?$this->errors[$name]:[]);
    }

    /**
     * Get all error
     * @return array
     */
    public function getAllError()
    {
        return $this->errors;
    }

    /**
     * Get error as string with separator
     * @param  string $separator
     * @param  string $separator2
     * @return string
     */
    public function getAllErrorString($separator = '<br>', $separator2 = '<br>')
    {
        $errors = [];
        foreach ($this->errors as $key => $value) {
            $errors[] = implode($separator, $value);
        }

        return implode($separator2, $errors);
    }

    /**
     * Clear error
     */
    public function clearError(){
        $this->errors = [];
    }

    /**
     * Get filter
     * @return array
     */
    public function getFilter()
    {
        return $this->filters;
    }

    /**
     * Set default filter status
     */
    public function setDefaultFilter($status)
    {
        $this->defaultFilter = $status;
    }

    /**
     * Default filter status
     * @return boolean
     */
    public function getDefaultFilter()
    {
        return $this->defaultFilter;
    }

    /**
     * Find by primary key
     * @return Object $this
     */
    public function findByPK()
    {
        $pks = $this->getPrimaryKey();
        if (!is_array($pks)) {
            $pks = [$pks];
        }
        $args = func_get_args();
        $filter = [''];
        foreach ($pks as $field) {
            $filter[0] .= ($filter[0]?' and ':'').$field.' = ?';
            $filter[] = array_shift($args);
        }

        $this->load($filter, ['limit'=>1]);

        return $this;
    }

    /**
     * Get primary key(s)
     * @return string|array
     */
    public function getPrimaryKey()
    {
        if (!$this->primaryKeys) {
            $pks = [];
            foreach ($this->fields as $field => $schema) {
                if ($schema['pkey']) {
                    $pks[] = $field;
                }
            }

            return $this->primaryKeys = (count($pks)>1?$pks:reset($pks));
        }

        return $this->primaryKeys;
    }

    /**
     * Get primary key value
     * @return string|array
     */
    public function getPrimaryKeyValue()
    {
        $pks = $this->getPrimaryKey();
        if (!is_array($pks)) {
            $pks = [$pks];
        }

        foreach ($pks as $key => $value) {
            $pks[$value] = $this->get($value);
            unset($pks[$key]);
        }

        return $pks;
    }

    /**
     * Get table name
     * @return string
     */
    public function getTableName()
    {
        if (!$this->tableName) {
            $x = explode('\\', get_called_class());

            return $this->tableName = Base::instance()->snakecase(lcfirst(end($x)));
        }

        return $this->tableName;
    }

    /**
     * @override reset
     */
    public function reset()
    {
        parent::reset();
        $this->clearError();
    }

    public function __construct()
    {
        parent::__construct(Connection::getConnection($this->defaultConnection), $this->getTableName(), $this->defaultField, $this->ttl);
    }
}