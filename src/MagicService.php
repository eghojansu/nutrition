<?php

namespace Nutrition;

use ArrayAccess;
use Base;
use Prefab;
use RuntimeException;

abstract class MagicService extends Prefab implements ArrayAccess
{
    const E_METHOD = 'No method "%s" in %s';

    /**
    *   Return TRUE if key is not empty
    *   @return bool
    *   @param $key string
    **/
    abstract function exists($key);

    /**
    *   Bind value to key
    *   @return mixed
    *   @param $key string
    *   @param $val mixed
    **/
    abstract function set($key,$val);

    /**
    *   Retrieve contents of key
    *   @return mixed
    *   @param $key string
    *   @param $default mixed
    **/
    abstract function &get($key, $default = null);

    /**
    *   Unset key
    *   @return NULL
    *   @param $key string
    **/
    abstract function clear($key);

    /**
    *   Convenience method for checking property value
    *   @return mixed
    *   @param $key string
    **/
    function offsetexists($key) {
        return Base::instance()->visible($this,$key)?
            isset($this->$key):$this->exists($key);
    }

    /**
    *   Convenience method for assigning property value
    *   @return mixed
    *   @param $key string
    *   @param $val scalar
    **/
    function offsetset($key,$val) {
        return Base::instance()->visible($this,$key)?
            ($this->$key=$val):$this->set($key,$val);
    }

    /**
    *   Convenience method for retrieving property value
    *   @return mixed
    *   @param $key string
    **/
    function &offsetget($key) {
        if (Base::instance()->visible($this,$key))
            $val=&$this->$key;
        else
            $val=&$this->get($key);
        return $val;
    }

    /**
    *   Convenience method for removing property value
    *   @return NULL
    *   @param $key string
    **/
    function offsetunset($key) {
        if (Base::instance()->visible($this,$key))
            unset($this->$key);
        else
            $this->clear($key);
    }

    /**
    *   Alias for offsetexists()
    *   @return mixed
    *   @param $key string
    **/
    function __isset($key) {
        return $this->offsetexists($key);
    }

    /**
    *   Alias for offsetset()
    *   @return mixed
    *   @param $key string
    *   @param $val scalar
    **/
    function __set($key,$val) {
        return $this->offsetset($key,$val);
    }

    /**
    *   Alias for offsetget()
    *   @return mixed
    *   @param $key string
    **/
    function &__get($key) {
        $val=&$this->offsetget($key);
        return $val;
    }

    /**
    *   Alias for offsetunset()
    *   @return NULL
    *   @param $key string
    **/
    function __unset($key) {
        $this->offsetunset($key);
    }

    /**
     * Proxy un-exists method call to its correspondent method
     * @param  string $name
     * @param  array  $params
     * @return mixed
     *
     * @throws RuntimeException
     */
    public function __call($name, array $params)
    {
        if (preg_match('/^(get|is|set|exists|clear)(.+)$/', $name, $matches)) {
            $method = $matches[1] === 'is' ? 'get' : $matches[1];
            array_unshift($params, lcfirst($matches[2]));

            return call_user_func_array([$this, $method], $params);
        }

        throw new RuntimeException(sprintf(self::E_METHOD, $name, static::class));
    }
}
