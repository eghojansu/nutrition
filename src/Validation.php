<?php

namespace Nutrition;

/**
 * Validation
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

use Base;
use DB\Cursor;
use DB\SQL\Mapper as SQLMapperOri;
use DB\Jig\Mapper as JigMapperOri;
use DB\Mongo\Mapper as MongoMapperOri;
use PDO;

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
     * @var  array
     */
    protected $lookup;
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
    /**
     * Field labels
     *
     * @var array
     */
    protected $labels = [];

    public function __construct(Cursor $map = null, array $lookup = [])
    {
        $this->map = $map;
        $this->lookup = $lookup;
        $this->messages = Base::instance()->get('validation_messages');
    }

    /**
     * Label
     * @param array $labels
     */
    public function setLabels(array $labels)
    {
        $this->labels = $labels;

        return $this;
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
            '{label}'=>$this->getLabel($field),
            '{value}'=>$this->getValue()
        ];
        foreach ($args as $key => $value) {
            $pattern['{args_'.++$ctr.'}'] = is_array($value)?implode(', ', $value):$value;
        }
        $this->errors[$field][] = str_replace(array_keys($pattern), array_values($pattern), $message);

        return $this;
    }

    /**
     * Get error
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get error
     *
     * @param  string $field
     * @return string
     */
    public function getError($field)
    {
        return isset($this->errors[$field])?implode(', ', $this->errors[$field]):null;
    }

    /**
     * Check if no errors
     *
     * @return boolean
     */
    public function valid()
    {
        return !$this->hasError();
    }

    /**
     * Check errors
     *
     * @return boolean
     */
    public function hasError()
    {
        return count($this->errors) > 0;
    }

    /**
     * Add filter
     *
     * @param string
     * @param string
     * @param mixed
     */
    public function addFilter($field, $filter, $args = null)
    {
        if (!isset($this->filters[$field])) {
            $this->filters[$field] = [];
        }
        if (false === is_string($filter) && is_callable($filter)) {
            $this->filters[$field][] = $filter;
        } else {
            $this->filters[$field][$filter] = is_array($args)?$args:[$args];
        }

        return $this;
    }

    /**
     * Remove filter
     *
     * @param  string $field
     * @param  array  $filters
     * @return object $this
     */
    public function removeFilter($field, array $filters = [])
    {
        if (isset($this->filters[$field])) {
            if ($filters) {
                foreach ($filters as $filter) {
                    unset($this->filters[$field][$filter]);
                }
            } else {
                unset($this->filters[$field]);
            }
        }

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
     * @return object $this
     */
    public function validate()
    {
        if (empty($this->map)) {
            return true;
        }

        foreach ($this->filters as $field => $filters) {
            $this->validateField($field, $filters);
        }

        return $this;
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
     * @param  int $max
     * @param  int $length max length
     * @return bool
     */
    protected function validationMaxInt($max = null, $length = null)
    {
        $number    = $this->getValue();
        $isInt     = is_numeric($number) && is_int($number * 1);
        $maxPassed = $isInt && (is_null($max) || $number <= $max);
        $lenPassed = $isInt && (is_null($length) || strlen($number) <= $length);

        return (bool) ((''===$number || is_null($number)) || ($maxPassed && $lenPassed));
    }

    /**
     * Validate integer
     * @param  int $min
     * @param  int $length max length
     * @return bool
     */
    protected function validationMinInt($min = null, $length = null)
    {
        $number    = $this->getValue();
        $isInt     = is_numeric($number) && is_int($number * 1);
        $minPassed = $isInt && (is_null($min) || $number >= $min);
        $lenPassed = $isInt && (is_null($length) || strlen($number) <= $length);

        return (bool) ((''===$number || is_null($number)) || ($minPassed && $lenPassed));
    }

    /**
     * Validate float
     * @param  float $max
     * @param  int $length max length
     * @return bool
     */
    protected function validationMaxFloat($max = null, $length = null)
    {
        $number    = $this->getValue();
        $isNumber  = is_numeric($number);
        $maxPassed = $isNumber && (is_null($max) || $number <= $max);
        $lenPassed = $isNumber && (is_null($length) || strlen($number) <= $length+1);

        return (bool) ((''===$number || is_null($number)) || ($maxPassed && $lenPassed));
    }

    /**
     * Validate float
     * @param  float $min
     * @param  int $length max length
     * @return bool
     */
    protected function validationMinFloat($min = null, $length = null)
    {
        $number    = $this->getValue();
        $isNumber  = is_numeric($number);
        $minPassed = $isNumber && (is_null($min) || $number >= $min);
        $lenPassed = $isNumber && (is_null($length) || strlen($number) <= $length+1);

        return (bool) ((''===$number || is_null($number)) || ($minPassed && $lenPassed));
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
     * @param  int $max
     * @param  bool $mayEmpty
     * @return bool
     */
    protected function validationMaxLength($max = null, $mayEmpty = false)
    {
        $value     = $this->getValue();
        $length    = strlen($value);
        $mayEmpty &= ('' === $value || is_null($value));
        $maxPassed = is_null($max) || $length <= $max;

        return (bool) ($mayEmpty || $maxPassed);
    }

    /**
     * Validate string
     * @param  int $min
     * @param  bool $mayEmpty
     * @return bool
     */
    protected function validationMinLength($min = null, $mayEmpty = false)
    {
        $value     = $this->getValue();
        $length    = strlen($value);
        $mayEmpty &= ('' === $value || is_null($value));
        $minPassed = is_null($min) || $length >= $min;

        return (bool) ($mayEmpty || $minPassed);
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

        if (!$mayEmpty) {
            $field || $field = $this->cursor;

            // assume mapper in same namespace
            if (false === strpos($mapNamespace, '\\') && $this->map) {
                $ns = get_class($this->map);
                $pos = strrpos($ns, '\\');
                $mapNamespace = ($pos === false ? '' : substr($ns, 0, $pos+1)).$mapNamespace;
            }

            $map = new $mapNamespace;
            $options = ['limit'=>1];
            if ($map instanceOf SQLMapperOri) {
                $filter = ["$field = ?", $value];
            }
            elseif ($map instanceOf JigMapperOri) {
                $filter = [$field=>$value];
            }
            elseif ($map instanceOf MongoMapperOri) {
                $filter = ["@{$field} = ?", $value];
            }
            else {
                user_error('Invalid mapper instance');
            }
            $map->load($filter, $options);

            return $map->valid();
        }

        return (bool) $mayEmpty;
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
        if ($map instanceOf SQLMapperOri) {
            $filter = ["$field = ?", $value];
        }
        elseif ($map instanceOf JigMapperOri) {
            $filter = [$field=>$value];
        }
        elseif ($map instanceOf MongoMapperOri) {
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
                $pa[$key] = $this->map->getPrevious($key);
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
     * Reverse date with delimiter and glue
     * @return bool
     */
    protected function validationReverseDate($delimiter = null, $glue = null)
    {
        $delimiter = $delimiter?:'-';
        $glue = $glue?:'-';

        $value    = $this->getValue();
        $x = explode($delimiter, $value);
        krsort($x);

        return implode($glue, $x);
    }

    /**
     * Get current field value
     * @return mixed
     */
    protected function getValue()
    {
        return $this->cursor ? (
            ($this->map && $this->map->exists($this->cursor)) ? $this->map->get($this->cursor) : (
                isset($this->lookup[$this->cursor])?$this->lookup[$this->cursor]:null)
            ) : null;
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
            if (is_numeric($filter)) {
                $filter = $args;
                $args = [];
            }
            if (is_callable($args)) {
                $args = call_user_func($args);
            }
            if (!is_array($args)) {
                $args = [$args];
            }
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
        if (is_string($filter)) {
            $method = 'validation'.ucfirst($filter);
            if (method_exists($this, $method)) {
                return [$this, $method];
            } elseif (method_exists($this->map, $filter)) {
                return [$this->map, $filter];
            } else {
                user_error('Method '.$filter.' cannot used for validation');
            }
        } if (is_callable($filter)) {
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
            if ($schema['pkey'] && PDO::PARAM_INT === $schema['pdo_type']) {
                continue;
            }

            $filters = [];
            $filters['required'] = [!$schema['nullable']];
            if (preg_match('/^(?<type>\w+)(?:\((?<length>.+)\))?/', $schema['type'], $match)) {
                $length = isset($match['length'])?$match['length']:null;
                switch ($match['type']) {
                    case 'int':
                    case 'bigint':
                    case 'smallint':
                    case 'tinyint':
                    case 'integer':
                        $filters['maxInt'] = [null, $length];
                        break;
                    case 'decimal':
                    case 'double':
                    case 'float':
                    case 'real':
                        $x = $app->split($length);
                        $base = pow(10, $x[0]-$x[1])-1;
                        $precision = (pow(10, $x[1]) - 1)*1/pow(10, $x[1]);
                        $max = $base + $precision;
                        $filters['maxFloat'] = [$max, $length];
                        break;
                    case 'enum':
                    case 'set':
                        $filters['choices'] = [$app->split(str_replace(['"', "'"], '', $length)), $schema['nullable']];
                        break;
                    case 'date':
                        $filters['date'] = [$schema['nullable']];
                        break;
                    default:
                        $filters['maxLength'] = [$length, $schema['nullable']];
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

    /**
     * Get label
     *
     * @param  string
     * @return string
     */
    protected function getLabel($field)
    {
        return isset($this->labels[$field])?$this->labels[$field]:ucwords(str_replace('_', ' ', $field));
    }
}
