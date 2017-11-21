<?php

namespace Nutrition\Utils;

use Base;
use Prefab;

abstract class Menu extends Prefab
{
    /** @var string current route name */
    private $route;


    /**
     * Get current route
     * @return string
     */
    public function getCurrent()
    {
        if (empty($this->route)) {
            $this->route = Base::instance()->get('ALIAS');
        }

        return $this->route;
    }

    /**
     * Set current route
     * @param string $route
     */
    public function setCurrent($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Is route active
     * @param  string  $route
     * @return boolean
     */
    public function isActive($route)
    {
        return $this->getCurrent() === $route;
    }

    abstract public function getMenu($nav);
}
