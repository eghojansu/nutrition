<?php

namespace Nutrition\SQL;

use Base;
use DB\SQL\Mapper as BaseMapper;
use DateTime;
use Nutrition\SQL\ConnectionBuilder;
use Nutrition\SQL\Criteria;
use Nutrition\Utils\CommonUtil;
use Nutrition\Utils\Pagination;
use RuntimeException;

/**
 * Wrapper class for DB\SQL\Mapper
 */
abstract class Mapper extends BaseMapper
{
    /** @var array Extra properties */
    protected $extras = [];

    /**
     * Clas constructor
     */
    public function __construct()
    {
        parent::__construct(
            ConnectionBuilder::instance()->getConnection(),
            $this->source ?: static::tableName()
        );

        $this->trigger = [
            'load'         => [$this, 'onMapLoad'],
            'beforeinsert' => [$this, 'onMapBeforeInsert'],
            'afterinsert'  => [$this, 'onMapAfterInsert'],
            'beforeupdate' => [$this, 'onMapBeforeUpdate'],
            'afterupdate'  => [$this, 'onMapAfterUpdate'],
            'beforeerase'  => [$this, 'onMapBeforeErase'],
            'aftererase'   => [$this, 'onMapAfterErase'],
        ];
    }

    /**
     * Get table name
     * @return string
     */
    public static function tableName()
    {
        return CommonUtil::snakeCase(trim(strrchr(static::class, '\\'), '\\'));
    }

    /**
     * Create static instance
     * @return static
     */
    public static function create()
    {
        return new static;
    }

    /**
     * DateTime to sql timestamp
     * @param  DateTime|null $date
     * @return string
     */
    public static function sqlTimestamp(DateTime $date = null)
    {
        $date = $date ?: new DateTime();

        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Proxy to finder method
     * @param  string $name
     * @param  array  $args
     * @return mixed
     */
    public function __call($name, $args)
    {
        if ('findoneby' === strtolower(substr($name, 0, 9))) {
            $field = CommonUtil::snakeCase(substr($name, 9));

            return $this->findone(Criteria::create()->add($field, array_shift($args))->get());
        } elseif ('findby' === strtolower(substr($name, 0, 6))) {
            $field = CommonUtil::snakeCase(substr($name, 6));

            return $this->find(Criteria::create()->add($field, array_shift($args))->get());
        }

        return parent::__call($name, $args);
    }

    /**
     * Expose connection
     * @return DB\SQL
     */
    public function connection()
    {
        return $this->db;
    }

    /**
     * @see DB\Cursor::paginate
     * @return Nutrition\Utils\Pagination
     */
    public function createPagination(...$params)
    {
        $subset = call_user_func_array([$this, 'paginate'], $params);

        return new Pagination($subset);
    }

    /**
     * Override parent to handle extra property
     * @param string $key
     * @param mixed $val
     */
    public function set($key,$val)
    {
        if (array_key_exists($key, $this->extras)) {
            $this->extras[$key] = $val;

            return $val;
        }

        return parent::set($key, $val);
    }

    /**
     * Override parent to handle extra property
     * @param  string $key
     * @return mixed
     */
    public function &get($key)
    {
        if (array_key_exists($key, $this->extras)) {
            return $this->extras[$key];
        }

        return parent::get($key);
    }

    /**
     * Override parent method to accept custom property assigment
     * @param  mixed $var
     * @param  callable $func
     * @return void
     */
    public function copyfrom($var,$func=NULL)
    {
        if (is_string($var)) {
            $var = Base::instance()->get($var);
        }
        if ($func) {
            $var = call_user_func($func, $var);
        }
        foreach ($var as $key=>$val) {
            if (
                array_key_exists($key, $this->fields)
                || array_key_exists($key, $this->extras)
            ) {
                $this->set($key,$val);
            }
        }
    }

    /**
     * Override parent to include custom property
     * @param  string $key HIVE member
     * @return void
     */
    public function copyto($key)
    {
        $var =& Base::instance()->ref($key);
        foreach ($this->fields+$this->adhoc as $key=>$field) {
            $var[$key]=$field['value'];
        }
        $var += $this->extras;
    }

    /**
     * On map load, invoked by mapper factory
     * @param  $this $that
     * @return void
     */
    public function onMapLoad($that)
    {
        // do something
    }

    /**
     * On map before insert, invoked by insert
     * @param  $this $that
     * @param  array  $pkeys
     * @return null|boolean false to prevent insert
     */
    public function onMapBeforeInsert($that, array $pkeys)
    {
        // do something
    }

    /**
     * On map after insert, invoked by insert
     * @param  $this $that
     * @param  array  $pkeys
     * @return void
     */
    public function onMapAfterInsert($that, array $pkeys)
    {
        // do something
    }

    /**
     * On map before update, invoked by update
     * @param  $this $that
     * @param  array  $pkeys
     * @return null|boolean false to prevent update
     */
    public function onMapBeforeUpdate($that, array $pkeys)
    {
        // do something
    }

    /**
     * On map after update, invoked by update
     * @param  $this $that
     * @param  array  $pkeys
     * @return void
     */
    public function onMapAfterUpdate($that, array $pkeys)
    {
        // do something
    }

    /**
     * On map before erase, invoked by erase
     * @param  $this $that
     * @param  array  $pkeys
     * @return null|boolean false to prevent erase
     */
    public function onMapBeforeErase($that, array $pkeys)
    {
        // do something
    }

    /**
     * On map after erase, invoked by erase
     * @param  $this $that
     * @param  array  $pkeys
     * @return void
     */
    public function onMapAfterErase($that, array $pkeys)
    {
        // do something
    }
}
