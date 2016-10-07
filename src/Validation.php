<?php

namespace Nutrition;

/**
 * Validation
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

use Base;
use DB\SQL\Mapper as SQLMapper;
use DB\Jig\Mapper as JigMapper;
use DB\Mongo\Mapper as MongoMapper;

class Validation
{
    /**
     * Validation rules to inspect
     * @var array
     */
    protected $filters = [];
    /**
     * Error
     * @var array
     */
    protected $errors = [];
    /**
     * DB\Cursor
     * @var DB\Cursor
     */
    protected $map;
    /**
     * Default messages
     * @var  array
     */
    protected $messages = [];
    /**
     * Current field position
     * @var string
     */
    protected $cursor;

    public function __construct(MapperInterface $map = null)
    {
        $this->map = $map;
        $this->messages = Base::instance()->get('validation_messages');
    }

    /**
     * Add error
     *
     * @param string
     * @param string
     * @param array
     */
    public function addError($field, $message, $args)
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }

        $pattern = [
            '{field}'=>$field,
            '{value}'=>$this->getValue()
        ];
        foreach ($args as $key => $value) {
            $pattern['{args_'.++$ctr.'}'] = is_array($value)?implode(', ', $value):$value;
        }
        $this->errors[$field][] = str_replace(array_keys($pattern), array_values($pattern), $message);

        return $this;
    }

    /**
     * Add filter
     *
     * @param string
     * @param string
     * @param array
     */
    public function addFilter($field, $filter, $args)
    {
        if (!isset($this->filters[$field])) {
            $this->filters[$field] = [];
        }
        $this->filters[$field][$filter] = $args;

        return $this;
    }

    /**
     * Get filter
     *
     * @param  string
     * @return array
     */
    public function getFilter($field)
    {
        return isset($this->filters[$field])?$this->filters[$field]:[];
    }

    /**
     * Get filters
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Perform validation
     * @return boolean
     */
    public function validate()
    {
        foreach ($this->filters as $field => $filters) {
            $this->validateField($field, $filters);
        }

        return !$this->map->hasError();
    }

    /**
     * Get filter message pattern
     * @param  string $filter
     * @return string
     */
    public function getMessage($filter)
    {
        return isset($this->messages[$filter])?$this->messages[$filter]:'{label} tidak valid.';
    }

    /**
     * Validate required
     * @param  bool $required negate purposes
     * @return bool
     */
    protected function validationRequired($required = true)
    {
        $value     = $this->getValue();
        $available = (isset($value) && '' !== $value);

        return (bool) ($available?:!$required);
    }

    /**
     * Validate integer
     * @param  int $min
     * @param  int $max
     * @param  int $length max length
     * @return bool
     */
    protected function validationInteger($min = null, $max = null, $length = null)
    {
        $number    = $this->getValue();
        $isInt     = is_numeric($number) && is_int($number * 1);
        $minPassed = $isInt && (is_null($min) || $number >= $min);
        $maxPassed = $isInt && (is_null($max) || $number <= $max);
        $lenPassed = $isInt && (is_null($length) || strlen($number) <= $length);

        return (bool) ((''===$number || is_null($number)) || ($minPassed && $maxPassed && $lenPassed));
    }

    /**
     * Validate float
     * @param  float $min
     * @param  float $max
     * @param  int $length max length
     * @return bool
     */
    protected function validationFloat($min = null, $max = null, $length = null)
    {
        $number    = $this->getValue();
        $isNumber  = is_numeric($number);
        $minPassed = $isNumber && (is_null($min) || $number >= $min);
        $maxPassed = $isNumber && (is_null($max) || $number <= $max);
        $lenPassed = $isNumber && (is_null($length) || strlen($number) <= $length+1);

        return (bool) ((''===$number || is_null($number)) || ($minPassed && $maxPassed && $lenPassed));
    }

    /**
     * Validate choices
     * @param  array $choices
     * @param  bool  $mayEmpty
     * @return bool
     */
    protected function validationChoices()
    {
        $args  = func_get_args();
        if (is_array(reset($args))) {
            $choices = array_shift($args);
            $mayEmpty = (bool) end($args);
        } else {
            $mayEmpty = is_bool(end($args))?array_pop($args):false;
            $choices = $args;
        }
        $value    = trim($this->getValue());
        $mayEmpty = ($mayEmpty && ('' === $value || is_null($value)));
        $exists   = $choices?in_array($value, $choices):true;

        return (bool) ($mayEmpty || $exists);
    }

    /**
     * Validate string
     * @param  int $min
     * @param  int $max
     * @param  bool $mayEmpty
     * @return bool
     */
    protected function validationString($min = null, $max = null, $mayEmpty = false)
    {
        $value     = $this->getValue();
        $length    = strlen($value);
        $mayEmpty &= ('' === $value || is_null($value));
        $minPassed = is_null($min) || $length >= $min;
        $maxPassed = is_null($max) || $length <= $max;

        return (bool) ($mayEmpty || ($minPassed && $maxPassed));
    }

    /**
     * Lookup in other namespace
     * @param  string $mapNamespace Nutrition\DB\SQL\AbstractMapper
     * @param  string $field        used field
     * @param  bool $mayEmpty
     * @return bool
     */
    protected function validationLookup($mapNamespace, $field = null, $mayEmpty = false)
    {
        $value = $this->getValue();
        $mayEmpty &= empty($value);
        $field || $field = $this->cursor;

        // assume mapper in same namespace
        if (false === strpos($mapNamespace, '\\')) {
            $mapNamespace = getClass($this->map).'\\'.$mapNamespace;
        }

        $map = new $mapNamespace;
        $options = ['limit'=>1];
        if ($map instanceOf SQLMapper) {
            $filter = ["$field = ?", $value];
        }
        elseif ($map instanceOf JigMapper) {
            $filter = [$field=>$value];
        }
        elseif ($map instanceOf MongoMapper) {
            $filter = ["@{$field} = ?", $value];
        }
        else {
            user_error('Invalid mapper instance');
        }
        $map->load($filter, $options);

        return (bool) ($mayEmpty || $map->valid());
    }

    /**
     * Check unique current map
     * @return bool
     */
    protected function validationUnique($primaryKey)
    {
        $value = $this->getValue();
        $field = $this->cursor;

        $map = clone $this->map;
        $options = ['limit'=>1];
        if ($map instanceOf SQLMapper) {
            $filter = ["$field = ?", $value];
        }
        elseif ($map instanceOf JigMapper) {
            $filter = [$field=>$value];
        }
        elseif ($map instanceOf MongoMapper) {
            $filter = ["@{$field} = ?", $value];
        }
        else {
            user_error('Invalid mapper instance');
        }
        $map->load($filter, $options);

        $result = $map->dry();

        if (!$result && $this->map->valid()) {
            $pa = [];
            $pb = [];
            $primaryKey = is_array($primaryKey)?$primaryKey:[$primaryKey];
            foreach ($primaryKey as $key) {
                $pa[$key] = $this->map->get($key);
                $pb[$key] = $map->get($key);
            }

            $result = ($pa === $pb);
        }

        return (bool) $result;
    }

    /**
     * Check match validation
     * @param  string  $pattern  regexp
     * @param  boolean $mayEmpty
     * @return bool
     */
    protected function validationMatch($pattern, $mayEmpty = false)
    {
        $value = $this->getValue();
        $mayEmpty &= ('' === $value || is_null($value));

        return (bool) ($mayEmpty || preg_match($pattern, $value));
    }

    /**
     * Check date
     * @param  boolean $mayEmpty
     * @return bool
     */
    protected function validationDate($mayEmpty = false)
    {
        $value    = $this->getValue();
        $mayEmpty &= ('' === $value || is_null($value));
        $pattern  = '/^\d{4}\-\d{2}\-\d{2}$/';

        return (bool) ($mayEmpty || preg_match($pattern, $value));
    }

    /**
     * Get current field value
     * @return mixed
     */
    protected function getValue()
    {
        return $this->cursor?$this->map->get($this->cursor):null;
    }

    /**
     * Validate field filters
     * @param  string $field
     * @param  array  $filters
     */
    protected function validateField($field, array $filters)
    {
        $this->cursor = $field;
        foreach ($filters as $filter => $args) {
            is_array($args) || $args = [$args];
            $callable = $this->resolveMethod($filter);
            $out = call_user_func_array($callable, $args);
            if (is_bool($out)) {
                if (!$out) {
                    $this->addError($field, $this->getMessage($filter), $args);
                }
            } else {
                $this->map->set($field, $out);
            }
        }
    }

    /**
     * Resolve method
     * @param  string $filter
     * @return string|array callable
     */
    protected function resolveMethod($filter)
    {
        $method = 'validation'.ucfirst($filter);
        if (method_exists($this, $method)) {
            return [$this, $method];
        } elseif (method_exists($this->map, $filter)) {
            return [$this->map, $filter];
        } elseif (is_callable($filter)) {
            return $filter;
        } else {
            user_error('Method '.$filter.' cannot used for validation');
        }
    }

    /**
     * Resolve default filter and assign default value if field not changed
     */
    public function resolveDefaultFilter()
    {
        $app = Base::instance();
        foreach ($this->map?$this->map->schema():[] as $field => $schema) {
            $filters = [];
            $filters['required'] = !$schema['nullable'];
            if (preg_match('/^(?<type>\w+)(?:\((?<length>.+)\))?/', $schema['type'], $match)) {
                $length = isset($match['length'])?$match['length']:null;
                switch ($match['type']) {
                    case 'int':
                    case 'bigint':
                    case 'smallint':
                    case 'tinyint':
                    case 'integer':
                        $filters['integer'] = [null, null, $length];
                        break;
                    case 'decimal':
                    case 'double':
                    case 'float':
                    case 'real':
                        $x = $app->split($length);
                        $base = pow(10, $x[0]-$x[1])-1;
                        $precision = (pow(10, $x[1]) - 1)*1/pow(10, $x[1]);
                        $max = $base + $precision;
                        $filters['float'] = [null, $max, $length];
                        break;
                    case 'enum':
                    case 'set':
                        $filters['choices'] = [$app->split(str_replace(['"', "'"], '', $length)), $schema['nullable']];
                        break;
                    case 'date':
                        $filters['date'] = $schema['nullable'];
                        break;
                    default:
                        $filters['string'] = [null, $length, $schema['nullable']];
                        break;
                }
            }
            $this->filters[$field] = $filters;
            if (!$schema['changed'] && (is_null($schema['value']) || ''===$schema['value']) && !(is_null($schema['default']) || ''===$schema['default'])) {
                $this->map->set($field, $schema['default']);
            }
        }

        return $this;
    }
}
