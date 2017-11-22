<?php

namespace Nutrition\Utils;

use DateTime;

class CommonUtil
{
    /**
     * Decide which to return
     * @param  boolean $true
     * @param  mixed $ifTrue
     * @param  mixed $ifFalse
     * @return mixed
     */
    public static function decide($true, $ifTrue, $ifFalse = null)
    {
        return $true ? $ifTrue : $ifFalse;
    }

    /**
     * SQL timestamp to date format
     * @param  string $date
     * @param  string $format
     * @return string
     */
    public static function dateSQL($date, $format = 'd-m-Y')
    {
        if (empty($date)) {
            return null;
        }

        $obj = DateTime::createFromFormat('Y-m-d H:i:s', $date);
        if (empty($obj)) {
            return null;
        }

        return $obj->format($format);
    }

    /**
     * Compare boolean value
     * @param  scalar $value
     * @param  mixed $onTrue
     * @param  mixed $onFalse
     * @return mixed
     */
    public static function trueFalse($value, $onTrue = 'True', $onFalse = 'False')
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? $onTrue : $onFalse;
    }

    /**
     * @see trueFalse
     */
    public static function onOff($value)
    {
        return static::trueFalse($value, 'On', 'Off');
    }

    /**
     * @see trueFalse
     */
    public static function yesNo($value)
    {
        return static::trueFalse($value, 'Yes', 'No');
    }

    /**
     * Check and get post value
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public static function postValue($key, $default = null)
    {
        return array_key_exists($key, $_POST) ? $_POST[$key] : $default;
    }

    /**
     * Get random string
     * @param  integer $len
     * @return string
     */
    public static function random($len = 6)
    {
        $chars  = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $random = '';
        for ($p = 0; $p < $len; $p++) {
            $random .= $chars[mt_rand(0, 61)];
        }

        return $random;
    }

    /**
     * Check if str has prefix
     * @param  string $prefix
     * @param  string $str
     * @return boolean
     */
    public static function startsWith($prefix, $str)
    {
        return substr($str, 0, strlen($prefix)) === $prefix;
    }

    /**
     * Check if str has suffix
     * @param  string $suffix
     * @param  string $str
     * @return boolean
     */
    public static function endsWith($suffix, $str)
    {
        return substr($str, -1*strlen($suffix)) === $suffix;
    }

    /**
     * Check length
     * @param  int $len
     * @param  string $str
     * @param  string $compare
     * @return bool
     */
    public static function length($len, $str, $compare = null)
    {
        $actualLen = strlen($str);
        switch ($compare) {
            case 'lte':
            case '<=':
                return $actualLen <= $len;
            case 'gte':
            case '>=':
                return $actualLen >= $len;
            case 'lt':
            case '<':
                return $actualLen < $len;
            case 'gt':
            case '>':
                return $actualLen > $len;
            default:
                return $actualLen == $len;
        }
    }

    /**
     * Get label that key is lower than value
     * @param  numeric $value
     * @param  string[]  $labels with numeric key
     * @return string
     */
    public static function lowerLabel($value, array $labels)
    {
        ksort($labels);

        foreach ($labels as $max => $label) {
            if ($value <= $max) {
                return $label;
            }
        }

        return end($labels);
    }

    /**
     * Dump argument
     * @return void
     */
    public static function dump(...$vars)
    {
        $line = str_repeat('-', 80);
        echo '<pre>' . PHP_EOL;
        echo $line . PHP_EOL;
        foreach ($vars as $var) {
            var_dump($var);
            echo PHP_EOL;
        }
        echo $line . PHP_EOL;
        echo '</pre>';
    }

    /**
     * snake_case to Title Case
     * @param  string $str
     * @return string
     */
    public static function titleCase($str)
    {
        return ucwords(str_replace('_', ' ', trim($str)));
    }

    /**
     * PascalCase to snake_case
     * @param  string $str
     * @return string
     */
    public static function snakeCase($str)
    {
        return strtolower(preg_replace('/(?!^)\p{Lu}/u', '_\0', $str));
    }

    /**
     * snake_case to PascalCase
     * @param  string $str
     * @return string
     */
    public static function pascalCase($str)
    {
        return ucfirst(preg_replace_callback(
            '/_(\pL)/u',
            function($match) {
                return strtoupper($match[1]);
            },
            $str
        ));
    }

    /**
     * snake_case to camelCase
     * @param  string $str
     * @return string
     */
    public static function camelCase($str)
    {
        return preg_replace_callback(
            '/_(\pL)/u',
            function($match) {
                return strtoupper($match[1]);
            },
            $str
        );
    }

    /**
     * Get major version
     * @param  string $version
     * @return string
     */
    public static function majorVersion($version)
    {
        $components = explode('.', $version);
        foreach ($components as $key => $value) {
            if ($key > 0 && is_numeric($value)) {
                $components[$key] = '0';
            }
        }

        return implode('.', $components);
    }

    /**
     * Get minor version
     * @param  string $version
     * @return string
     */
    public static function minorVersion($version)
    {
        $components = explode('.', $version);
        foreach ($components as $key => $value) {
            if ($key > 1 && is_numeric($value)) {
                $components[$key] = '0';
            }
        }

        return implode('.', $components);
    }
}
