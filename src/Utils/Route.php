<?php

namespace Nutrition\Utils;

use Base;
use Prefab;
use RuntimeException;

class Route extends Prefab
{
    const E_ROUTE = 'Route "%s" was not exists';


    /**
     * Build path
     * @param  string  $path
     * @param  array $queries
     * @return string
     */
    public function path($path, array $queries = null)
    {
        return Base::instance()->get('BASE') . '/' . $path .
            $this->addQueries($queries);
    }

    /**
     * Build route
     * @param  string  $route
     * @param  mixed  $params
     * @param  array $queries
     * @return string
     */
    public function build($route, $params = null, array $queries = null)
    {
        $base = Base::instance();
        $url = $base["ALIASES.$route"];
        if (empty($url)) {
            throw new RuntimeException(sprintf(static::E_ROUTE, $route));
        }
        $params = (array) $params;

        return $base['BASE'] . $this->replaceToken($url, $params).
            $this->addQueries($queries);
    }

    /**
     * Get current path
     * @param  mixed  $params
     * @param  boolean $withParams
     * @param  array $queries
     * @return string
     */
    public function currentPath($params = null, $withParams = true, array $queries = null)
    {
        $base = Base::instance();

        return $this->build(
            $base['ALIAS'],
            ((array) $params) + ($withParams?$base['PARAMS']:[]),
            $queries
        );
    }

    /**
     * Add current queries
     * @param array|null $queries
     */
    protected function addQueries(array $queries = null)
    {
        return $queries ? '?'.http_build_query($queries) : '';
    }

    /**
     * Build route
     * @param  string $path
     * @param  array  $params
     * @return string
     */
    protected function replaceToken($path, array $params)
    {
        $i=0;
        $path = preg_replace_callback('/@(\w+)|(\*)/', function($match) use(&$i,$params) {
            if (isset($match[1]) &&
                array_key_exists($match[1],$params)) {
                return $params[$match[1]];
            }
            if (isset($match[2]) &&
                array_key_exists($match[2],$params)) {
                if (!is_array($params[$match[2]])) {
                    return $params[$match[2]];
                }

                return $params[$match[2]][$i-1];
            }

            return $match[0];
        }, $path);

        return $path;
    }
}
