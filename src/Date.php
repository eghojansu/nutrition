<?php

/**
 * This file is part of eghojansu/nutrition
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

namespace Nutrition;

use Base;
use Nutrition\DB\SQL\AbstractMapper;
use DateTime;

/**
 * Extended DateTime
 * You can pass months and days via Fatfree global variabel NDATE
 */
class Date
{
    private static $months = [1=>'Januari','Februari','Maret','April','Mei','Juni',
        'Juli','Agustus','September','Oktober','November','Desember'];
    private static $days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

    /**
     * Get month names
     * set via NDATE.MONTHS
     * @return array
     */
    public static function months()
    {
        return Base::instance()->get('NDATE.MONTHS')?:self::$months;
    }

    /**
     * Get month names
     * set via NDATE.DAYS
     * @return array
     */
    public static function days()
    {
        return Base::instance()->get('NDATE.DAYS')?:self::$days;
    }

    /**
     * Get greeting
     * @return string
     */
    public static function greeting()
    {
        $hour = (int) date('H');
        if ($hour < 12)
            return 'good_morning';
        elseif ($hour < 16)
            return 'good_afternoon';
        elseif ($hour < 20)
            return 'good_evening';
        else
            return 'good_night';
    }

    /**
     * Get day name
     * @param  int $no
     * @return string
     */
    public static function dayName($no)
    {
        if (!is_numeric($no)) {
            try {
                $date = new DateTime($no);
                $no = $date->format('w');
            } catch(Exception $e) {
                return null;
            }
        }

        $no *= 1;
        $days = self::days();

        return isset($days[$no])?$days[$no]:null;
    }

    /**
     * Get month name
     * @param  int $no
     * @return string
     */
    public static function monthName($no)
    {
        if (!is_numeric($no)) {
            try {
                $date = new DateTime($no);
                $no = $date->format('n');
            } catch(Exception $e) {
                return null;
            }
        }

        $no *= 1;
        $months = self::months();

        return isset($months[$no])?$months[$no]:null;
    }

    /**
     * Read date
     * @param  string $sqlDate
     * @return string
     */
    public static function readDate($sqlDate)
    {
        $xdate = explode('-', $sqlDate);

        return $sqlDate==='0000-00-00'?null:((int) $xdate[2]).' '.self::monthName($xdate[1]).' '.$xdate[0];
    }
}