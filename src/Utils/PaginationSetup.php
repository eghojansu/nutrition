<?php

namespace Nutrition\Utils;

use Base;
use Prefab;

class PaginationSetup extends Prefab
{
    /** @var string current route name */
    private $route;

    /** @var array */
    private $routeParams = [];

    /** @var string page route argument name */
    private $pageArgName = 'page';

    /** @var int */
    private $requestPage;

    /** @var int */
    private $perpage = 10;

    /** @var int */
    private $adjacent = 3;


    /**
     * Get perpage
     * @return int
     */
    public function getPerpage()
    {
        return $this->perpage;
    }

    /**
     * Set perpage
     * @param int $perpage
     */
    public function setPerpage($perpage)
    {
        $this->perpage = $perpage;

        return $this;
    }

    /**
     * Get adjacent
     * @return int
     */
    public function getAdjacent()
    {
        return $this->adjacent;
    }

    /**
     * Set adjacent
     * @param int $adjacent
     */
    public function setAdjacent($adjacent)
    {
        $this->adjacent = $adjacent;

        return $this;
    }

    /**
     * Get route
     * @return string
     */
    public function getRoute()
    {
        if (empty($this->route)) {
            $this->route = Base::instance()->get('ALIAS');
        }

        return $this->route;
    }

    /**
     * Set route
     * @param string $route
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route params
     * @return array
     */
    public function getRouteParams()
    {
        if (empty($this->routeParams)) {
            $this->routeParams = Base::instance()->get('PARAMS');
            unset($this->routeParams[0]);
        }

        return $this->routeParams;
    }

    /**
     * Set route params
     * @param array $routeParams
     */
    public function setRouteParams($routeParams)
    {
        $this->routeParams = $routeParams;

        return $this;
    }

    /**
     * Get request page
     * @param  integer $default
     * @return int
     */
    public function getRequestPage($default = 1)
    {
        if (empty($this->requestPage)) {
            $this->requestPage = abs($this->getRequestArg(
                $this->pageArgName,
                $default
            ));
        }

        return $this->requestPage;
    }

    /**
     * Set request page
     * @param int $page
     */
    public function setRequestPage($page)
    {
        $this->requestPage = $page;

        return $this;
    }

    /**
     * Get request arg ($_GET)
     * @param  string $name
     * @param  mixed $default
     * @return mixed
     */
    public function getRequestArg($name, $default = null)
    {
        return Base::instance()->get("GET.$name") ?: $default;
    }

    /**
     * Get page arg name
     * @return string
     */
    public function getPageArgName()
    {
        return $this->pageArgName;
    }

    /**
     * Set page arg name
     * @param string $name
     */
    public function setPageArgName($name)
    {
        $this->pageArgName = $name;

        return $this;
    }

    /**
     * Build pagination route
     * @param  bool $disable
     * @param  int $page
     * @return string
     */
    public function path($disable, $page)
    {
        if ($disable) {
            return '#';
        }

        return Route::instance()->build(
            $this->getRoute(),
            $this->getRouteParams(),
            [$this->pageArgName => $page] + ($_GET?:[])
        );
    }
}
