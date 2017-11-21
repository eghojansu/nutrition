<?php

namespace Nutrition\Test\Fixture;

use Base;

class FileSystemHelper
{
    public static function prepareDir()
    {
        $dir = self::tempDir().'/file-system-'.time();

        @mkdir($dir, Base::MODE, true);

        return $dir;
    }

    public static function logDir()
    {
        return self::tempDir().'/logs';
    }

    public static function tempDir()
    {
        return __DIR__.'/../../var';
    }
}
