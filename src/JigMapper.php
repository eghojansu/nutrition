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

    /**
     * Field labels
     *
     * @var array
     */
    protected $labels = [];

    public function __construct($file = null)
    {
        $db = Base::instance()->get($this->connection);
        parent::__construct($db, $file?:$this->file);
    }

    /**
     * Get field label
     *
     * @param  string $field
     * @return string
     */
    public function getLabel($field)
    {
        if (empty($this->labels[$field])) {
            $this->labels[$field] = ucfirst(implode(' ', explode('_', $field)));
        }

        return $this->labels[$field];
    }

    /**
     * Get labels
     *
     * @return array
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * Get previous field value
     *
     * @param  string $field
     * @return string
     */
    public function getPrevious($field)
    {
        return $this->get($field);
    }
}
