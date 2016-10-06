<?php

namespace Nutrition;

use DB\SQL\Mapper;

class SQLMapper extends Mapper
{
    /**
     * Connection key
     *
     * @var string
     */
    protected $connection = 'DB.SQL';

    public function __construct($table = null, $fields = null, $ttl = 60)
    {
        $db = Base::instance()->get($this->connection);
        parent::__construct($db, $table?:$this->source, $fields, $ttl);
    }

    /**
     * Generate new ID based on format
     * @param string $columName
     * @param string $format
     * @return string
     */
    public function nextID($columnName, $format)
    {
        $clone = clone $this;
        $clone->load(null, [
            'limit'=>1,
            'order'=>$columnName.' desc',
            ]);

        $last = 0;
        $boundPattern = '/\{([a-z0-9\- _\.]+)\}/i';
        if ($clone->valid()) {
            $pattern = preg_replace_callback($boundPattern, function($match) {
                return is_numeric($match[1])?
                    '(?<serial>'.str_replace('9', '[0-9]', $match[1]).')':
                    '(?<date>.{'.strlen(date($match[1])).'})';
            }, $format);
            if (preg_match('/^'.$pattern.'$/i', $clone[$columnName], $match))
                $last = $match['serial']*1;
        }

        return preg_replace_callback($boundPattern, function($match) use ($last) {
            return is_numeric($match[1])?
                str_pad($last+1, strlen($match[1]), '0', STR_PAD_LEFT):
                date($match[1]);
        }, $format);
    }
}
