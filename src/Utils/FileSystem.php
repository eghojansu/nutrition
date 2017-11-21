<?php

namespace Nutrition\Utils;

use Base;

class FileSystem
{
    protected $dir;

    /**
     * Create filesystem for dir
     * @param string $dir
     */
    public function __construct($dir)
    {
        $this->dir = rtrim(Base::instance()->fixslashes($dir),'/');
    }

    /**
     * Create static
     * @param  string $dir
     * @return static
     */
    public static function create($dir)
    {
        return new static($dir);
    }

    /**
     * Get dir
     * @return string
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * Get real path
     * @return string
     */
    public function getRealpath()
    {
        return realpath($this->dir);
    }

    /**
     * Remove dir
     * @param bool $withFoler
     * @return $this
     */
    public function removeDir($withFolder = false)
    {
        if (!is_dir($this->dir)) {
            return $this;
        }

        foreach (glob($this->dir.'/*') as $item) {
            if (is_dir($item)) {
                self::create($item)->removeDir(true);
            } else {
                @unlink($item);
            }
        }
        if ($withFolder) {
            @rmdir($this->dir);
        }

        return $this;
    }

    /**
     * Remove file in this dir
     * @param  string $file
     * @return $this
     */
    public function removeFile($file)
    {
        @unlink($this->dir.$file);

        return $this;
    }

    /**
     * Perform glob function
     * @param  string $pattern
     * @return array
     */
    public function getContents($pattern = '*')
    {
        return glob($this->dir.'/'.$pattern);
    }
}
