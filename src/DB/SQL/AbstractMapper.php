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
use Nutrition;
use Nutrition\DB\MapperInterface;
use Nutrition\DB\Validation;
use Nutrition\InvalidRuntimeException;

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
     * Enable/disable default validation
     * @var boolean
     */
    protected $defaultValidation = true;
    /**
     * Validation rules
     * @var array
     */
    protected $rules = [];
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
     * Filters
     * @var array
     */
    protected $filters = [''];
    /**
     * options
     * @var array
     */
    protected $options = [];

    /**
     * @override DB\SQL\Mapper->select
     */
    public function select($fields,$filter=NULL,array $options=NULL,$ttl=0)
    {
        return parent::select($fields, $this->getFilter($filter), $this->getOption($options), $this->getTTL($ttl));
    }

    /**
     * @override DB\SQL\Mapper->find
     */
    public function find($filter=NULL,array $options=NULL,$ttl=0)
    {
        return parent::find($this->getFilter($filter), $this->getOption($options), $this->getTTL($ttl));
    }

    /**
     * @override DB\SQL\Mapper->count
     */
    public function count($filter=NULL,$ttl=0)
    {
        return parent::count($this->getFilter($filter), $this->getTTL($ttl));
    }

    /**
     * @override DB\SQL\Mapper->erase
     */
    public function erase($filter=NULL)
    {
        return parent::erase($this->getFilter($filter), $this->getTTL($ttl));
    }

    /**
     * @override DB\SQL\Mapper->findone
     */
    public function findone($filter=NULL,array $options=NULL,$ttl=0)
    {
        return parent::findone($this->getFilter($filter), $this->getOption($options), $this->getTTL($ttl));
    }

    /**
     * @override DB\SQL\Mapper->paginate
     */
    public function paginate($pos=0,$size=10,$filter=NULL,array $options=NULL,$ttl=0)
    {
        return parent::paginate($pos, $size, $this->getFilter($filter), $this->getOption($options), $this->getTTL($ttl));
    }

    /**
     * @override DB\SQL\Mapper->load
     */
    public function load($filter=NULL,array $options=NULL,$ttl=0)
    {
        return parent::load($this->getFilter($filter), $this->getOption($options), $this->getTTL($ttl));
    }

    /**
     * Add filter
     * @param array $filters
     *        ['field', $value, 'operator(eg: =,>,<,begin,contain,end,etc)', 'and|or']
     *        or
     *        [
     *            ['field', $value, 'operator(eg: =,>,<,begin,contain,end,etc)', 'and|or'],
     *            ['field', $value, 'operator(eg: =,>,<,begin,contain,end,etc)', 'and|or'],
     *        ]
     */
    public function addFilter(array $filters)
    {
        $filterSchema = [1 => null, '=', 'and'];
        $first        = reset($filters);
        is_array($first) || $filters = [$filters];
        $filterString = '';
        $filterData   = [];
        $conjunction  = '';
        foreach ($filters as $filter) {
            $filter    += $filterSchema;
            $filterStr  = $filter[0];
            if (!isset($filter[1]) || is_null($filter[1]) || '' == $filter[1]) {
                continue;
            }

            switch (strtolower($filter[2])) {
                case 'begin':
                    $filterStr .= ' like ?';
                    $filter[1]  = '%'.$filter[1];
                    break;
                case 'contain':
                    $filterStr .= ' like ?';
                    $filter[1]  = '%'.$filter[1].'%';
                    break;
                case 'end':
                    $filterStr .= ' like ?';
                    $filter[1]  = $filter[1].'%';
                    break;
                default:
                    $filterStr .= ' '.$filter[2].' ?';
                    break;
            }
            $filterData[] = $filter[1];

            if ($conjunction) {
                $filterStr = ' '.$filter[3].' '.$filterStr;
            } else {
                $conjunction = $filter[3];
            }

            $filterString .= $filterStr;
        }

        $this->filters[0] = $filterString?($this->filters[0]?' '.$conjunction.' ':'').'('.$filterString.')':'';
        $this->filters    = array_merge($this->filters, $filterData);

        return $this;
    }

    /**
     * AddFilter alias
     * @see  addFilter method
     */
    public function where(array $filters)
    {
        return $this->addFilter($filters);
    }

    /**
     * Get filter and reset it
     * @param  array $args
     * @return array
     */
    public function getFilter($args = null)
    {
        $filters = $this->filters;
        $this->filters = [''];

        return $args?:(array_filter($filters)?:null);
    }

    /**
     * Set TTL
     * @param int $ttl
     */
    public function setTTL($ttl)
    {
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * Get TTL
     * @param  int $ttl
     * @return int
     */
    public function getTTL($ttl = null)
    {
        return $ttl?:$this->ttl;
    }

    /**
     * Add option
     * @param string $name
     * @param mixed $val
     */
    public function addOption($name, $val)
    {
        $this->options[$name] = $val;

        return $this;
    }

    /**
     * orderBy
     * @param  string|int $val
     */
    public function orderBy($val)
    {
        $this->options['order'] = $val;

        return $this;
    }

    /**
     * groupBy
     * @param  string|int $val
     */
    public function groupBy($val)
    {
        $this->options['group'] = $val;

        return $this;
    }

    /**
     * limit
     * @param  string|int $val
     */
    public function limit($val)
    {
        $this->options['limit'] = $val;

        return $this;
    }

    /**
     * offset
     * @param  string|int $val
     */
    public function offset($val)
    {
        $this->options['offset'] = $val;

        return $this;
    }

    /**
     * Get option and reset it
     * @param  mixed $args
     * @return array
     */
    public function getOption($args = null)
    {
        if (isset($this->options['offset']) && !isset($this->options['limit'])) {
            throw new InvalidRuntimeException('You must pass limit when use offset option');
        }

        $options = $this->options;
        $this->options = [];

        return $args?:(array_filter($options)?:null);
    }

    /**
     * Validate this map
     * @param  string $mode group filter
     * @return bool
     */
    public function validate($mode = 'default')
    {
        return (new Validation($this, $mode))->validate();
    }

    /**
     * Save with validation
     * @param  string $mode group filter
     * @return bool
     */
    public function safeSave($mode = 'default')
    {
        if ($this->validate($mode)) {
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
            $this->labels[$field] = Nutrition::titleIze($field);
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
     * Get rules
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Add rule
     * @param string $field
     * @param string $rule
     * @return  object $this
     */
    public function addRule($field, $rule)
    {
        if (!$this->ruleExists($field, $rule)) {
            $this->rules[$field] = isset($this->rules[$field])?$this->rules[$field].','.trim($rule, ','):$rule;
        }

        return $this;
    }

    /**
     * Check if rule was exists
     * @param  string $field
     * @param  string $rule
     * @return bool
     */
    public function ruleExists($field, $rule)
    {
        $x = explode('(', $rule);
        $pattern = '/'.preg_quote($x[0], '/').'/i';

        return isset($this->rules[$field]) && preg_match($pattern, $this->rules[$field]);
    }

    /**
     * Set default validation status
     * @param bool $status
     * @return  object $this
     */
    public function setDefaultValidation($status)
    {
        $this->defaultValidation = $status;

        return $this;
    }

    /**
     * Default validation status
     * @return boolean
     */
    public function getDefaultValidation()
    {
        return $this->defaultValidation;
    }

    /**
     * Find by primary key
     * @param  mixed $ids
     * @return Object $this
     */
    public function findByPK($args)
    {
        if (!is_array($args)) {
            $pk = $this->getPrimaryKey();
            $args = [$pk=>$args];
        }
        $filter = [''];
        foreach ($args as $field => $value) {
            $filter[0] .= ($filter[0]?' and ':'').$field.' = ?';
            $filter[] = $value;
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
            return $this->tableName = Nutrition::classNameToTable(get_called_class());
        }

        return $this->tableName;
    }

    /**
     * Get default field to select
     * @return null|string
     */
    public function getDefaultField()
    {
        return $this->defaultField;
    }

    /**
     * Set default field
     * @param string $fields
     * @return object $this
     */
    public function setDefaultField($fields)
    {
        $this->defaultField = $fields;
        // re-construct
        self::__construct();

        return $this;
    }

    /**
     * Init mapper, call in mapper creation
     */
    protected function init()
    {
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
        $this->init();
    }
}