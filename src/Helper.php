<?php

namespace Nutrition;

use DateTime;

class Helper
{
    public static $days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    public static $months = [1=>'Januari','Februari','Maret','April','Mei',
        'Juni','Juli','Agustus','September','Oktober','November','Desember'
    ];
    public static $romans = [1=>'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];

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
     * Number format wrapper
     * @param  number $no
     * @param  int $decimal
     * @return string
     */
    public static function number($no, $decimal = 2)
    {
        return number_format($no, $decimal, ',', '.');
    }

    /**
     * Normalize number to save in db
     * @param  number $no
     * @return string
     */
    public static function normalizeNumber($no)
    {
        return false === strpos($no, ',') ? $no :
            str_replace(['.',','], ['','.'], $no);
    }

    /**
     * Get days
     *
     * @return array
     */
    public static function days()
    {
        return self::$days;
    }

    /**
     * Get days
     *
     * @return array
     */
    public static function months()
    {
        return self::$months;
    }

    /**
     * Get days
     *
     * @return array
     */
    public static function romans()
    {
        return self::$romans;
    }

    /**
     * Join date
     * @param  array  $date
     * @return string
     */
    public static function joinDate(array $date)
    {
        ksort($date);

        return implode('-', $date);
    }

    /**
     * Reverse
     * @param  string  $date
     * @return string
     */
    public static function reverseDate($date, $delimiter = '-', $glue = '-')
    {
        $x = explode($delimiter, $date);
        krsort($x);

        return implode($glue, $x);
    }

    /**
     * Get month name
     * @param  int $no
     * @return string
     */
    public static function monthName($no)
    {
        $no *= 1;

        return isset(self::$months[$no])?self::$months[$no]:null;
    }

    /**
     * Get day name
     * @param  int $no
     * @return string
     */
    public static function dayName($no)
    {
        $no *= 1;

        return isset(self::$days[$no])?self::$days[$no]:null;
    }

    /**
     * Read date in indonesian
     * @param  string $tanggal mysql format string
     * @return string
     */
    public static function tanggal($tanggal)
    {
        if (!$tanggal) {
            return null;
        }

        $date = new DateTime($tanggal);

        return $date->format('d').' '.self::$months[$date->format('n')].' '.$date->format('Y');
    }

    /**
     * Read date and day in indonesian
     * @param  string $tanggal mysql format string
     * @return string
     */
    public static function hariTanggal($tanggal)
    {
        $date = new DateTime($tanggal);

        return self::$days[$date->format('w')].', '.$date->format('d')
            .' '.self::$months[$date->format('n')].' '.$date->format('Y');
    }

    /**
     * Read day from two date(format)
     * @param  string $tanggal_a mysql format string
     * @param  string $tanggal_b mysql format string
     * @return string
     */
    public static function hariKeHari($tanggal_a, $tanggal_b)
    {
        $date_a = new DateTime($tanggal_a);
        $date_b = new DateTime($tanggal_b);

        return self::$days[$date_a->format('w')].' - '.self::$days[$date_b->format('w')];
    }

    /**
     * Read date from two date(format)
     * @param  string $tanggal_a mysql format string
     * @param  string $tanggal_b mysql format string
     * @return string
     */
    public static function tanggalKeTanggal($tanggal_a, $tanggal_b)
    {
        $date_a = new DateTime($tanggal_a);
        $date_b = new DateTime($tanggal_b);

        return $date_a->format('d').' - '.$date_b->format('d').' '.self::$months[$date_a->format('n')].' '.$date_a->format('Y');
    }

    /**
     * Get roman month
     * @param  string $tanggal
     * @return string
     */
    public static function romanMonth($tanggal)
    {
        $date = $tanggal instanceof DateTime ?$tanggal:new DateTime($tanggal);

        return self::$romans[$date->format('n')];
    }

    /**
     * Get roman text
     * @param  string $no
     * @return string
     */
    public static function roman($no)
    {
        return isset(self::$romans[$no])?self::$romans[$no]:null;
    }

    /**
     * Count age
     * @param  string $date valid
     * @param string $format date interval format
     * @return string
     */
    public static function age($date, $format = '%y tahun')
    {
        if (!$date) {
            return null;
        }

        $date = new DateTime($date);
        $diff = $date->diff(new DateTime);

        return $diff->format($format);
    }
}
