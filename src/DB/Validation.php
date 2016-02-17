<?php

namespace Nutrition\DB;

/**
 * Validation
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

use Base;
use Nutrition\InvalidMethodException;

class Validation
{
	/**
	 * Validation rules to inspect
	 * @var array
	 */
	protected $filters = [];
	/**
	 * MapperInterface
	 * @var MapperInterface
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

    public function __construct(MapperInterface $map, $mode = 'default')
    {
        $this->map = $map;
        $this->map->clearError();
        $this->resolveFilter(strtolower($mode));
        $this->messages = Base::instance()->get('validation_messages');
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
     * @param  bool $negate
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

        return (bool) ($minPassed && $maxPassed && $lenPassed);
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

        return (bool) ($minPassed && $maxPassed && $lenPassed);
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

        $map = new $mapNamespace;
        $map->load([$field.' = ?', $value], ['limit'=>1]);

        return (bool) ($mayEmpty || $map->valid());
    }

    /**
     * Check unique current map
     * @return bool
     */
    protected function validationUnique()
    {
        $value = $this->getValue();
        $field = $this->cursor;

        $map = clone $this->map;
        $map->load([$field.' = ?', $value], ['limit'=>1]);

        return (bool) ($map->dry() || ($this->map->valid() && $map->getPrimaryKeyValue() === $this->map->getPrimaryKeyValue()));
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
                    $this->map->addError($field, $this->getMessage($filter), $args);
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
     * @throws InvalidMethodException
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
            throw new InvalidMethodException('Method '.$filter.' cannot used for validation');
        }
    }

    /**
     * Resolve filter
     * @param  string $mode
     */
    protected function resolveFilter($mode)
    {
        $app = Base::instance();
    	if ($this->map->getDefaultValidation()) {
    		$this->resolveDefaultFilter($app);
    	}
    	foreach ($this->map->getRules() as $field => $filters) {
    		$this->parseFilter($field, $filters, $mode, $app);
    	}
    }

    /**
     * Resolve default filter and assign default value if field not changed
     * @param  Base $app
     */
    protected function resolveDefaultFilter(Base $app)
    {
    	foreach ($this->map->schema() as $field => $schema) {
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
    }

    /**
     * Parse string filter,
     * masih berpotensi error untuk membaca regexp
     * @param  string $field
     * @param  string $filterString
     * @param  string $mode
     * @param  Base $app
     */
    protected function parseFilter($field, $filterString, $mode, Base $app)
    {
        $pattern = '/((?<=\().+?(?=\)))|\w+/';
        if (preg_match_all($pattern, $filterString, $matches)) {
            $filters = [];
            for ($i=0, $length = count($matches[0]); $i < $length; $i++) {
                $filter = $matches[0][$i];
                $args = null;
                if (isset($matches[1][$i+1]) && $matches[1][$i+1]===$matches[0][$i+1]) {
                    // has argument
                    extract($this->resolveGroupArgs($matches[1][$i+1], $app));
                    if (in_array($mode, $groups)) {
                        $filters[$filter] = $args;
                    }
                    $i++;
                } else {
                    $filters[$filter] = $args;
                }
            }
            $this->filters[$field] = array_merge(isset($this->filters[$field])?$this->filters[$field]:[], $filters);
        }
    }

    /**
     * Resolve args and groups
     * @param  string $args
     * @param  Base   $app
     * @return array
     */
    protected function resolveGroupArgs($args, Base $app)
    {
        $resolved = ['groups'=>['default'], 'args'=>$args];
        if (preg_match('/(?<remove>on\h+(?<groups>[\w,\h]+))/i', $args, $matches)) {
            $resolved['groups'] = $app->split(strtolower($matches['groups']));
            $resolved['args'] = str_replace($matches['remove'], '', $args);
        }
        $resolved['args'] = $this->resolveArgs($app->split(trim($resolved['args'])));

        return $resolved;
    }

    /**
     * Resolve args value to real php value
     * @param  array  $args
     * @return array
     */
    protected function resolveArgs(array $args)
    {
        foreach ($args as $key => $value) {
            switch ($value) {
                case 'true':
                case 'on':
                    $args[$key] = true;
                    break;
                case 'false':
                case 'off':
                    $args[$key] = false;
                    break;
                case 'null':
                    $args[$key] = null;
                    break;
            }
        }

        return $args;
    }
}