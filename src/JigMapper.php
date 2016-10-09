<?php

namespace Nutrition;

use Base;
use DB\Jig\Mapper;

class JigMapper extends Mapper
{
    /**
     * Connection key
     *
     * @var string
     */
    protected $connection = 'DB.Jig';

    public function __construct($file = null)
    {
        $db = Base::instance()->get($this->connection);
        parent::__construct($db, $file?:$this->file);
    }
}
