<?php

namespace Nutrition\Test\SQL;

use MyTestCase;
use Nutrition\SQL\Criteria;

class CriteriaTest extends MyTestCase
{
    private $criteria;

    protected function setUp()
    {
        $this->criteria = new Criteria();
    }

    public function testGetCriteria()
    {
        $this->assertEquals(null, $this->criteria->get());
    }

    public function testCreate()
    {
        $this->assertInstanceOf(Criteria::class, Criteria::create());
    }

    public function testAddCriteria()
    {
        $this->assertEquals(['id = 1'], $this->criteria->addCriteria('id = 1')->get());
        $this->assertEquals(['id = 1 and name = :name', 'myname'],
            $this->criteria->addCriteria('name = :name', ['myname'])->get());
    }

    public function testAdd()
    {
        $this->assertEquals(['id = :id', ':id'=>'myname'],
            $this->criteria->add('id', 'myname', '=')->get());
        $this->assertEquals(['id = :id and name = :name', ':id'=>'myname', ':name'=>'noname'],
            $this->criteria->add('name','noname','=')->get());
        $this->assertEquals(['id = :id and name = :name or this', ':id'=>'myname', ':name'=>'noname'],
            $this->criteria->add('this', null, null, 'or')->get());
        $this->assertEquals(['id in (?,?)', 1, 2], Criteria::create()->add('id', [1,2])->get());
    }

    public function testBuildCriteria()
    {
        $this->assertEquals([
            '?,?,?', 'a', 'b', 'c',
        ], $this->criteria->buildCriteria(['a','b','c']));

        $this->assertEquals([
            ':a,:b,:c', ':a'=>'a', ':b'=>'b', ':c'=>'c',
        ], $this->criteria->buildCriteria(['a'=>'a','b'=>'b','c'=>'c']));
    }
}
