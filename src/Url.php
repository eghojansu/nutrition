<?php

namespace Nutrition;

use Base;
use Prefab;

class Url extends Prefab
{
    protected static $base;

    public function base()
    {
        if (empty(self::$base)) {
            $base = Base::instance();

            $scheme = $base->get('SCHEME');
            $port = $base->get('PORT');

            self::$base = $scheme.'://'.$_SERVER['SERVER_NAME'].
                ($port && $port!=80 && $port!=443?
                    (':'.$port):'');
        }

        return self::$base;
    }

    public function path($path, array $params = [])
    {
        $base = Base::instance();

        if (false === strpos($path, '/') && $p = $base->get('ALIASES.'.$path)) {
            $path = ltrim($p,'/');

            $i=0;
            $path=preg_replace_callback('/@(\w+)|\*/',
                function($match) use(&$i,&$params) {
                    $i++;
                    if (isset($match[1]) && array_key_exists($match[1],$params)) {
                        $p = $params[$match[1]];
                        unset($params[$match[1]]);

                        return $p;
                    }

                    return array_key_exists($i,$params)?
                        $params[$i]:
                        $match[0];
                },$path);
        }

        return '#'===$path[0]?$path:$base->get('BASE').'/'.$path.($params?'?'.http_build_query($params):'');
    }

    public function absolute($path, array $params = [])
    {
        return self::base().self::path($path, $params);
    }
}
