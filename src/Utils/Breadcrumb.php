<?php

namespace Nutrition\Utils;

use Base;
use Prefab;

class Breadcrumb extends Prefab
{
    /** @var array */
    private $hierarchy = [];

    /** @var string */
    private $lastKey;


    /**
     * Clear breadcrumb
     * @return $this
     */
    public function clear()
    {
        $this->hierarchy = [];
        $this->lastKey = null;

        return $this;
    }

    /**
     * Set breadcrumb root
     * @param string     $route
     * @param array|null $args
     * @param string     $label
     */
    public function setRoot($route, array $args = null, $label = null)
    {
        array_unshift($this->hierarchy, [
            'label' => $label ?: static::titleIze($route),
            'route' => $route,
            'args' => (array) $args
        ]);
        $this->lastKey = null;

        return $this;
    }

    /**
     * Append to breadcrumb
     * @param string     $route
     * @param array|null $args
     * @param string     $label
     */
    public function add($route, array $args = null, $label = null)
    {
        $this->hierarchy[] = [
            'label' => $label ?: static::titleIze($route),
            'route' => $route,
            'args' => (array) $args
        ];
        $this->lastKey = null;

        return $this;
    }

    /**
     * Add current route to breadcrumb
     * @param string $label
     */
    public function addCurrentRoute($label = null)
    {
        $base = Base::instance();
        $args = ($base['PARAMS'] ?: []) + ($base['GET'] ?: []);
        unset($args[0]);

        return $this->add($base['ALIAS'], $args, $label);
    }

    /**
     * Add Group to breadcrumb
     * @param GroupChecker $groupChecker
     * @param string       $firstLabel
     * @param string       $route
     * @param array|null   $routeArgs
     */
    public function addGroup(
        GroupChecker $groupChecker,
        $firstLabel = null,
        $route = null,
        array $routeArgs = null
    ) {
        $routeArgs = (array) $routeArgs;
        if (empty($route)) {
            $base = Base::instance();
            $routeArgs = ($base['PARAMS'] ?: []) + ($base['GET'] ?: []);
            $route = $base['ALIAS'];
            unset($routeArgs[0]);
        }

        $i = 0;
        foreach ($groupChecker->getGroups() as $label => $group) {
            if ($i === 0 && $firstLabel) {
                $label = $firstLabel;
            }

            $this->add($route, ['group' => $group] + $routeArgs, $label);
            $i++;

            if ($groupChecker->isEqual($group)) {
                break;
            }
        }

        return $this;
    }

    /**
     * Get current hierarchy
     * @return array
     */
    public function getContent()
    {
        return $this->hierarchy;
    }

    /**
     * Check if key is las key
     * @param  mixed  $key
     * @return boolean
     */
    public function isLast($key)
    {
        if (null === $this->lastKey) {
            end($this->hierarchy);
            $this->lastKey = key($this->hierarchy);
        }

        return $key === $this->lastKey;
    }

    /**
     * Check if has no content
     * @param  boolean $compare
     * @return boolean
     */
    public function isEmpty($compare = true)
    {
        return $compare === (count($this->hierarchy) === 0);
    }

    /**
     * Titleize
     * @param  string $str
     * @return string
     */
    protected static function titleIze($str)
    {
        return ucwords(str_replace('_', ' ', trim($str)));
    }
}
