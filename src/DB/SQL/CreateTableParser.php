<?php

/**
 * This file is part of eghojansu/nutrition
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

namespace Nutrition\DB\SQL;

/**
 * SQL create table parser
 */
class CreateTableParser
{
    /**
     * Mysql keywords
     * @var array
     */
    protected $mysqlKeywords = [
        'PRIMARY',
        'INDEX',
        'CONSTRAINT',
        'FOREIGN',
        'REFERENCES',
        'ON',
        'UNIQUE',
        'ENGINE',
        'KEY',
        ];

    /**
     * Parse table
     * @param  string $sql
     * @return array pair table and columns
     */
    public function parse($sql)
    {
        $tables = [];
        $patternTable = '/create table\s*(?:if not exists)*\s*(?<tablename>[`\w]+)\s*\((?<tabledef>[^;]+)/i';
        $patternDef = '/^\s*([`\w]+)/im';

        preg_match_all($patternTable, $sql, $matches, PREG_SET_ORDER);

        foreach ($matches?:[] as $key => $value) {
            $tablename = $this->clearName($value['tablename']);
            if (preg_match_all($patternDef, $value['tabledef'], $columns)) {
                $columns = $this->clearName($columns[1]);
            }
            $tables[$tablename] = $columns?:[];
        }

        return $tables;
    }

    /**
     * Clear name from backtick
     * @param  string|array $name
     * @return string|array
     */
    protected function clearName($name)
    {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                if (in_array(strtoupper($value), $this->mysqlKeywords)) {
                    unset($name[$key]);
                    continue;
                }

                $name[$key] = str_replace('`', '', $value);
            }

            return $name;
        }

        return str_replace('`', '', $name);
    }
}