<?php

namespace Nutrition;

use Base;
use DB\Mongo\Mapper;

class MongoMapper extends Mapper
{
    /**
     * Connection key
     *
     * @var string
     */
    protected $connection = 'DB.Mongo';

    public function __construct($collection = null)
    {
        $db = Base::instance()->get($this->connection);
        parent::__construct($db, $collection?:$this->collection);
    }
}
