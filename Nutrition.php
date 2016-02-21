<?php

/**
 * Nutrition helper
 *
 * @author Eko Kurniawan <fkurniawan@outlook.com>
 */
final class Nutrition
{
    /**
     * Base url
     * @var string
     */
    private static $baseUrl;
    /**
     * Module configuration map
     * @var  array
     */
    private static $modules = [
    ];

    /**
     * Init fatfree Base class and register some filter rule
     * @return Base instance
     */
    public static function bootstrap(array $opt = [], $config = 'app/config/configs.ini')
    {
        $app = Base::instance();
        $app->mset($opt);
        $app->config($config);

        Template::instance()->filter('url', 'Nutrition::url');
        Template::instance()->filter('asset', 'Nutrition::asset');

        foreach ($app->get('modules')?:[] as $module) {
            $module = strtolower($module);
            if (isset(self::$modules[$module])) {
                $app->config(self::$modules[$module], true);
            }
        }

        $app->set('app.user', new Nutrition\Security\User);

        return $app;
    }

    /**
     * Generate base url
     * @return string
     */
    public static function baseUrl()
    {
        $app = Base::instance();

        if (empty(self::$baseUrl)) {
            self::$baseUrl = $app['SCHEME'].'://'.$_SERVER['SERVER_NAME'].
                ($app['PORT'] && $app['PORT']!=80 && $app['PORT']!=443?
                        (':'.$app['PORT']):'').$app['BASE'];
        }

        return self::$baseUrl;
    }

    /**
     * Get current absolute url
     * @return string
     */
    public static function currentUrl()
    {
        $app = Base::instance();

        return self::baseUrl().$app->rel(urldecode($app->get('URI')));
    }

    /**
     * Generate url based on alias
     * @param string $alias
     * @param mixed $param
     * @return string
     */
    public static function url($alias, $params = null)
    {
        return self::baseUrl().Base::instance()->alias($alias, $params);
    }

    /**
     * Generate assets url
     * @param string $path
     * @return string
     */
    public static function asset($path)
    {
        return self::baseUrl().'/'.$path;
    }

    /**
     * Get or set flash session
     * @param string $var
     * @param mixed $val
     * @return $val
     */
    public static function flash($var, $val = null)
    {
        $app = Base::instance();
        $var = 'SESSION.'.$var;

        if (is_null($val)) {
            $val = $app->get($var);
            $app->clear($var);

            return $val;
        }

        return $app->set($var, $val);
    }

    /**
     * Send json
     * @param array $output
     */
    public static function jsonOut(array $output)
    {
        header('Content-type: application/json');

        echo json_encode($output);
        die;
    }

    /**
     * Prepend each array key with prefix
     * @param array $array
     * @param string $prefix
     * @return array
     */
    public static function prependKey(array $array, $prefix = ':')
    {
        return array_combine(array_map(function($a) use ($prefix) {
            return $prefix.$a;
        }, array_keys($array)), array_values($array));
    }

    /**
     * TitleIze string
     * @param  string $str
     * @return string
     */
    public static function titleIze($str)
    {
        return ucwords(implode(' ', array_filter(explode('_', Base::instance()->snakecase(lcfirst($str))))));
    }

    /**
     * Get class name from namespace
     * @param  string $ns
     * @return string
     */
    public static function className($ns)
    {
        $class = strrchr($ns, '\\');
        $class || $class = $ns;

        return ltrim($class, '\\');
    }

    /**
     * Classname to table
     * @param  string $ns namespaec
     * @return string
     */
    public static function classNameToTable($ns)
    {
        return Base::instance()->snakeCase(lcfirst(self::className($ns)));
    }

    /**
     * Get directory content
     * @param  string $dirname
     * @param  boolean $recursive
     * @return array
     */
    public static function dirContent($dirname, $recursive = false)
    {
        $dir = new DirectoryIterator($dirname);
        $content = [];
        foreach ($dir as $file)
            if (!$file->isDot()) {
                if ($file->isDir() && $recursive)
                    $content = array_merge($content, self::dirContent($file->getPathname(), $recursive));
                else
                    $content[] = $file->getPathname();
            }

        return $content;
    }

    /**
     * Get start number
     * @param  array  $page result of Fatfree\DB\SQL\Mapper
     * @return int
     */
    public static function paginationStartNumber(array $page)
    {
        return (int) ($page['pos'] * $page['limit'] + 1);
    }

    /**
     * Pager
     * @param  array  $page page returned from DB\Cursor
     * @param  string $alias current page
     * @param  mixed $aliasParam
     * @return null
     */
    public static function pagerPaginate(array $page, $alias = null, $aliasParam = null)
    {
        $app = Base::instance();
        $url = self::url($alias?:$app->get('ALIAS'), $aliasParam);
        $get = $app->get('GET');
        unset($get['pos']);
        $next = [
            'stat'=>'',
            'url'=>$url.'?'.http_build_query($get+['pos'=>$page['pos']+1]),
        ];
        $prev = [
            'stat'=>'',
            'url'=>$url.'?'.http_build_query($get+['pos'=>$page['pos']-1])
        ];
        $dis = [
            'stat'=>' class="disabled"',
            'url'=>'#'
        ];
        $pagePos = $page['pos']+1;
        $info = $page['total']?'page '.$pagePos.'/'.$page['count'].' ':null;
        if ($page['pos']+1>=$page['count']) {
            // disable next
            $next = $dis;
        }
        if ($page['pos']===0) {
            // disable prev
            $prev = $dis;
        }
        $pager = <<<PAGER
<nav class="page-control pull-right">
  <ul class="pager">
    <li class="page-control-info">{$info}total records : {$page[total]}</li>
    <li{$prev[stat]}><a href="{$prev[url]}">Previous</a></li>
    <li{$next[stat]}><a href="{$next[url]}">Next</a></li>
  </ul>
</nav>
PAGER;

        return $pager;
    }
}