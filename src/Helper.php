<?php

namespace Nutrition;

use DateTime;
use DirectoryIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Helper
{
    public static $days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    public static $months = [1=>'Januari','Februari','Maret','April','Mei',
        'Juni','Juli','Agustus','September','Oktober','November','Desember'
    ];
    public static $romans = [1=>'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];

    /**
     * Left pad
     * @param  int $input
     * @param  int $length
     * @param  string $prefix
     * @return string
     */
    public static function leftPad($input, $length, $prefix = '')
    {
        return $prefix.str_pad($input, $length, '0', STR_PAD_LEFT);
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
            if (is_dir($file)) {
                rmdir($file);
            }
            else {
                unlink($file);
            }
        }

        return $content;
    }

    /**
     * Remove dir fast version
     * @param  string $path
     * @return boolean
     */
    public static function removeDir2($path, $removeParent = true)
    {
        if (!$path || '.' === $path || '..' === $path || !file_exists($path)) {
            return false;
        }

        $it = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
        $entries = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

        foreach($entries as $entry) {
            if ($entry->isDir()){
                rmdir($entry->getRealPath());
            } else {
                unlink($entry->getRealPath());
            }
        }

        if ($removeParent) {
            rmdir($path);
        }

        return true;
    }

    /**
     * Copy a file, or recursively copy a folder and its contents
     * @author      Aidan Lister <aidan@php.net>
     * @version     1.0.1
     * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
     * @param       string   $source    Source path
     * @param       string   $dest      Destination path
     * @param       int      $permissions New folder creation permissions
     * @return      bool     Returns true on success, false on failure
     */
    public static function copyDir($source, $dest, $permissions = 0755)
    {
        $source = strtr($source, '\\', '/');
        $dest = strtr($dest, '\\', '/');

        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }

        // Simple copy for a file
        if (is_file($source)) {
            if (false !== ($last = strrpos($dest, '/'))) {
                $baseDest = substr($dest, 0, $last);
                @mkdir($baseDest, $permissions, true);
            }

            return copy($source, $dest);
        }

        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest, $permissions, true);
        }

        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Deep copy directories
            self::copyDir("$source/$entry", "$dest/$entry", $permissions);
        }

        // Clean up
        $dir->close();

        return true;
    }

    /**
     * Handle file upload, cannot handle multiple files
     * @param  string $key          $_FILES[$key]
     * @param  string &$filename
     * @param  array  $allowedTypes
     * @return bool
     */
    public static function handleFileUpload($key, &$filename, $allowedTypes = [])
    {
        $result = false;
        $isArray = isset($_FILES[$key]) && is_array($_FILES[$key]['error']);

        if ($isArray) {
            return $result;
        }

        if (isset($_FILES[$key]) &&
            UPLOAD_ERR_OK === $_FILES[$key]['error'] &&
            ($allowedTypes && in_array($_FILES[$key]['type'], $allowedTypes))) {
            $ext = strtolower(strrchr($_FILES[$key]['name'], '.'));
            $filename .= $ext;
            $result = move_uploaded_file($_FILES[$key]['tmp_name'], $filename);
        }

        return $result;
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
