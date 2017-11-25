<?php

namespace Nutrition\Test\Fixture;

use Nutrition\MagicService;

class MyMagicService extends MagicService
{
    private $properties = [];

    public function exists($key)
    {
        return array_key_exists($key, $this->properties);
    }

    public function set($key,$val)
    {
        $this->properties[$key] = $val;

        return $this;
    }

    public function &get($key, $default = null)
    {
        if ($this->exists($key)) {
            $value =& $this->properties[$key];
        } else {
            $value =& $default;
        }

        return $value;
    }

    public function clear($key)
    {
        unset($this->properties[$key]);
    }
}
