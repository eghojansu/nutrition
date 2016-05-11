<?php

/**
 * This file is part of eghojansu/nutrition
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

/**
 * Nutrition helper
 *
 * @author Eko Kurniawan <fkurniawan@outlook.com>
 */
final class Nutrition
{
    /**
     * Package Info
     */
    const PACKAGE = 'eghojansu/nutrition',
          VERSION = '2.0.2';

    /**
     * Base url
     * @var string
     */
    private static $baseUrl;
    /**
     * Usefull template shortcut
     * @var  array
     */
    private static $shortcuts = [
        'url' => 'Nutrition::url',
        'asset' => 'Nutrition::asset',
        'lower' => 'strtolower',
        'upper' => 'strtoupper',
        'ucfirst' => 'ucfirst',
        'ucwords' => 'ucwords',
        'lcfirst' => 'lcfirst',
    ];

    /**
     * Init fatfree Base class and register some filter rule
     * @var  array $opt
     * @var  string $config
     * @return Base instance
     */
    public static function bootstrap(array $opt = [], $config = 'app/config/configs.ini')
    {
        $app = Base::instance();
        $app->mset($opt);
        $app->config($config);

        $shortcuts = array_merge(self::$shortcuts, $app->get('TEMPLATE_SHORTCUTS')?:[]);
        $template = Template::instance();
        foreach ($shortcuts as $key => $value) {
            $template->filter($key, $value);
        }

        $sysConfig = [
            'PACKAGE' => self::PACKAGE,
            'VERSION' => self::VERSION,
            'app.user'=> new Nutrition\Security\User,
            'app.homepage'=> self::baseUrl(),
            ];
        $app->mset($sysConfig);

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
     * @param mixed $output
     * @param bool $stopOutput
     * @param array $headers
     */
    public static function jsonOut($output, $stopOutput = false, array $headers = ['Content-type'=>'application/json'])
    {
        foreach ($headers as $key => $value) {
            header($key.': '.$value);
        }

        echo is_string($output)?$output:json_encode($output);

        if ($stopOutput) {
            die;
        }
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
     * @param  boolean $includeHidden
     * @param  boolean $includeDir
     * @return array
     */
    public static function dirContent($dirname, $recursive = false, $includeHidden = false, $includeDir = false)
    {
        $content = [];
        if (!file_exists($dirname)) {
            return $content;
        }

        $dir = new DirectoryIterator($dirname);
        foreach ($dir as $file) {
            $filename = $file->getFilename();
            $hidden = '.' === $filename[0];
            $include = !($file->isDot() || (!$includeHidden && $hidden));
            if ($include) {
                if ($file->isDir()) {
                    if ($recursive) {
                        $content = array_merge($content, self::dirContent($file->getPathname(), true, $includeHidden, $includeDir));
                    }
                }
                else
                    $content[] = $file->getPathname();
            }
        }
        if ($includeDir) {
            array_push($content, realpath($dirname));
        }

        return $content;
    }

    /**
     * Remove dir
     * @param  string  $path
     * @param  boolean $removeParent
     * @param  boolean $removeHidden
     * @return array
     */
    public static function removeDir($path, $removeParent = false, $removeHidden = false)
    {
        $content = self::dirContent($path, true, $removeHidden, true);

        if (!$removeParent) {
            array_pop($content);
        }

        foreach ($content as $file) {
            unlink($file);
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
     * @param  array  $page result of Fatfree\DB\SQL\Mapper
     * @param  string $alias current page
     * @param  mixed $aliasParam
     * @return null
     */
    public static function pagerPaginate(array $page, $alias = null, $aliasParam = null)
    {
        $app = Base::instance();
        // set pagination start number
        $app->set('paginationStartNumber', self::paginationStartNumber($page));
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

    /**
     * Dump vars
     * @param  mixed  $var
     * @param  boolean $halt
     */
    public static function dump($var, $halt = false)
    {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';

        if ($halt) {
            die;
        }
    }

    /**
     * Say number in indonesian
     * note: this function can exhaust memory if $no greater than 1000000
     * (need improvement)
     * @param  float $no
     * @return string
     */
    public static function terbilang($no)
    {
        if (!is_numeric($no)) {
            return null;
        }

        $no *= 1;
        $minus = 0 > $no;
        $fraction = fmod($no, 1);
        $cacah = ['nol','satu','dua','tiga','empat','lima','enam','tujuh','delapan','sembilan','sepuluh','sebelas'];

        $no = abs($no);
        $no -= $fraction;

        if ($no < 12) {
            $result = $cacah[$no];
        } elseif ($no < 20) {
            $result = $cacah[$no-10].' belas';
        } else if ($no < 100) {
            $mod = $no % 10;
            $mul = floor($no / 10);

            $result = $cacah[$mul].' puluh '.$cacah[$mod];
        } else if ($no < 1000) {
            $mod = $no % 100;
            $mul = floor($no / 100);

            $result = $cacah[$mul].' ratus '.self::terbilang($mod);
        } else if ($no < 100000) {
            $mod = $no % 1000;
            $mul = floor($no / 1000);

            $result = self::terbilang($mul).' ribu '.self::terbilang($mod);
        } else if ($no < 1000000000) {
            $mod = $no % 1000000;
            $mul = floor($no / 1000000);

            $result = self::terbilang($mul).' juta '.self::terbilang($mod);
        } else {
            return $no * ($minus?-1:1);
        }

        $result = ($minus?'minus ':'').str_replace([' nol','satu ','sejuta'], ['','se','satu juta'], $result);

        if ($fraction) {
            $fraction = (string) $fraction;
            for ($i=0, $e=strlen($fraction); $i < $e; $i++) {
                if (0 === $i) {
                    $result .= ' koma ';
                } elseif ($i > 1) {
                    $result .= ' '.self::terbilang($fraction[$i]);
                }
            }
        }

        return $result;
    }
}