<?php

namespace Nutrition;

use Base;
use DB\SQL\Mapper;

class SQLMapper extends Mapper
{
    /**
     * Connection key
     *
     * @var string
     */
    protected $connection = 'DB.SQL';

    /**
     * Field labels
     *
     * @var array
     */
    protected $labels = [];

    /**
     * Mappers
     *
     * @var  array
     */
    protected $maps;

    public function __construct($table = null, $fields = null, $ttl = 60)
    {
        $db = Base::instance()->get($this->connection);
        parent::__construct($db, $table?:$this->source, $fields, $ttl);
        $this->init();
    }

    /**
     * Init
     *
     * @return  object $this
     */
    protected function init()
    {
        // do something on map creation

        return $this;
    }

    /**
     * Get relation
     *
     * @param  string $name
     * @return SQLMapper
     */
    public function map($name)
    {
        if (empty($this->maps[$name])) {
            return null;
        }

        if (!$this->maps[$name] instanceOf SQLMapper) {
            $map = $this->maps[$name];

            $this->maps[$name] = is_callable($map['class'])?call_user_func_array($map['class'], [$this]):new $map['class'];
            if (isset($map['key'])) {
                $map['key'] = is_array($map['key'])?$map['key']:[$map['key']];

                $filter = [''];
                foreach ($map['key'] as $key=>$pair) {
                    $filter[0] .= ($filter[0]?', ':'').(is_numeric($key)?$pair:$key).' = ?';
                    $filter[] = $this->get($pair);
                }
                $this->maps[$name]->load($filter, isset($map['option'])?$map['option']:null);
            }
        }

        return $this->maps[$name];
    }

    /**
     * Generate new ID based on format
     * @param string $columName
     * @param string $format
     * @param string|boolean $assign
     * @param array $filter
     * @return object|string
     */
    public function nextID($columnName, $format, $assign = false, array $filter = null)
    {
        $clone = clone $this;
        $clone->load($filter, [
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
            if (preg_match('/^'.$pattern.'$/i', $clone[$columnName], $match)) {
                $last = $match['serial']*1;
            }
        }

        $id = preg_replace_callback($boundPattern, function($match) use ($last) {
            return is_numeric($match[1])?
                str_pad($last+1, strlen($match[1]), '0', STR_PAD_LEFT):
                date($match[1]);
        }, $format);

        if ($assign) {
            $this->set(is_string($assign)?$assign:$columnName, $id);

            return $this;
        }

        return $id;
    }

    /**
     * Populate record and transform to key=value pair array
     * @param  string $key      column name as key
     * @param  string|callable|null|array $value    column name as value
     * @param  array  $criteria
     * @param  array $options
     * @return array
     */
    public function populate($key, $value = null, array $criteria = [], array $options = [])
    {
        $data = [];
        $records = $this->find($criteria, $options);
        foreach ($records as $record) {
            if (is_null($value)) {
                $v = $record[$key];
            } elseif (is_array($value)) {
                if (empty($value)) {
                    $v = $record->cast();
                } else {
                    $v = [];
                    foreach ($value as $k) {
                        if (!$record->exists($k)) {
                            user_error("Column $k was not exists");
                        }
                        $v[$k] = $record[$k];
                    }
                }
            } elseif (is_callable($value)) {
                $v = call_user_func_array($value, [$record]);
            } else {
                if (!$record->exists($value)) {
                    user_error("Column $value was not exists");
                }
                $v = $record[$value];
            }
            $data[$record[$key]] = $v;
        }

        return $data;
    }

    /**
     * Get field label
     *
     * @param  string $field
     * @return string
     */
    public function getLabel($field)
    {
        if (empty($this->labels[$field])) {
            $this->labels[$field] = ucfirst(implode(' ', explode('_', $field)));
        }

        return $this->labels[$field];
    }

    /**
     * Get labels
     *
     * @return array
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * Get previous field value
     *
     * @param  string $field
     * @return string
     */
    public function getPrevious($field)
    {
        return $this->fields[$field]['previous'];
    }
}
