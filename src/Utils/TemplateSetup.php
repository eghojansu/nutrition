<?php

namespace Nutrition\Utils;

use Nutrition\MagicService;

class TemplateSetup extends MagicService
{
    protected $properties = [];


    public function __call($name, array $args)
    {
        if (preg_match('/^(?<method>prefix|suffix)(?<key>.+)$/', $name, $matches)) {
            array_unshift($args, lcfirst($matches['key']));
            $method = 'add'.ucfirst($matches['method']);

            return call_user_func_array([$this, $method], $args);
        }

        return parent::__call($name, $args);
    }

    public function exists($name)
    {
        return array_key_exists($name, $this->properties);
    }

    public function &get($name, $default = null)
    {
        if ($this->exists($name)) {
            $ref =& $this->properties[$name];
        } else {
            $ref =& $default;
        }

        return $ref;
    }

    public function set($name, $value)
    {
        $this->properties[$name] = $value;

        return $this;
    }

    public function clear($name)
    {
        unset($this->properties[$name]);
    }

    public function addPrefix($name, $value, $sep = ' - ')
    {
        $this->properties[$name] = trim($value.$sep.$this->get($name, ''), $sep);

        return $this;
    }

    public function addSuffix($name, $value, $sep = ' - ')
    {
        $this->properties[$name] = trim($this->get($name, '').$sep.$value, $sep);

        return $this;
    }
}
