<?php

namespace Nutrition\Constraint;

use Bumbon\Validation\Constraint\AbstractConstraint;
use DB\SQL\Mapper;
use InvalidArgumentException;

class Unique extends AbstractConstraint
{
    const MESSAGE_DEFAULT = 'Nilai ini sudah digunakan.';

    public function __construct(array $option = [])
    {
        parent::__construct($option);

        $this->option += [
            'mapper' => null,
            'field' => 'ID',
            // primary key
            'id' => 'ID',
            // current primary key value
            'current_id' => null,
        ];
    }

    /**
     * {@inheritdoc}
    */
    public function validate()
    {
        if (null === $this->option['mapper'] || !is_subclass_of($this->option['mapper'], Mapper::class)) {
            throw new InvalidArgumentException('Constraint should be instance of '.Mapper::class);
        }
        if (null !== $this->value) {
            if (is_string($this->option['mapper'])) {
                $class = $this->option['mapper'];
                $mapper = new $class;
            } else {
                $mapper = $this->option['mapper'];
            }
            $result = $mapper->findone(["{$this->option[field]} = ?", $this->value]);
            $this->valid = $result ? ($this->option['current_id'] ?
                $this->option['current_id'] == $result->get($this->option['id']) : false) : true;
        }

        return $this;
    }
}
