<?php

namespace Nutrition\Console;

use Base;

/**
 * Console command tool
 */
abstract class Command
{
    /**
     * Get option ($_GET mapper)
     * @param  string $name
     * @param  string $shortcut
     * @param  mixed $default
     * @return mixed
     */
    protected function getOption($name, $shortcut = null, $default = null)
    {
        if ($shortcut && array_key_exists($shortcut, $_GET)) {
            return $_GET[$shortcut];
        }

        return array_key_exists($name, $_GET) ? $_GET[$name] : $default;
    }

    /**
     * Check option exists
     * @param  string  $name
     * @param  string  $shortcut
     * @return boolean
     */
    protected function hasOption($name, $shortcut = null)
    {
        return ($shortcut && array_key_exists($shortcut, $_GET)) ? true :
            array_key_exists($name, $_GET);
    }

    /**
     * Write line
     * @param  string $str
     */
    protected function write($str)
    {
        echo StringParser::create($str);
    }

    /**
     * Writel line with new line
     * @param  string $str
     */
    protected function writeln($str)
    {
        echo StringParser::create($str) . PHP_EOL;
    }

    /**
     * Write simple table
     * @param  array  $headers
     * @param  array  $rows
     */
    protected function writeTable(array $headers, array $rows)
    {
        $columnCount = count($headers);
        $rowCount = count($rows) + 1;
        $columns = array_fill(0, $columnCount, 0);
        $tables = [];

        foreach (array_merge([$headers], $rows) as $row) {
            $i = -1;
            $newRow = [];
            foreach ($row as $col) {
                $newRow[++$i] = StringParser::create($col)->getParsed();
                $newRow[$i]['len'] = 0;
                foreach ($newRow[$i] as $part) {
                    $newRow[$i]['len'] += strlen($part['original']);
                }
                $columns[$i] = max($columns[$i], $newRow[$i]['len']);
            }
            $tables[] = $newRow;
        }

        $i = -1;
        $line = str_repeat('-', array_sum($columns) + $columnCount + 2);
        foreach ($tables as $row) {
            if (++$i === 0) {
                echo $line . PHP_EOL;
            }
            $j = -1;
            foreach ($row as $col) {
                ++$j;
                $len = array_pop($col);
                foreach ($col as $part) {
                    echo ' ' . $part['colored'];
                    echo str_repeat(' ', $columns[$j] - $len + 1);
                }
            }

            if ($i === 0) {
                echo PHP_EOL;
                echo $line;
            }

            echo PHP_EOL;
            if ($i === $rowCount - 1) {
                echo $line . PHP_EOL;
            }
        }
    }

    abstract public static function registerSelf(Base $app);
}
