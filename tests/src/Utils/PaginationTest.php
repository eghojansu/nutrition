<?php

namespace Nutrition\Test\Utils;

use Base;
use MyTestCase;
use Nutrition\Utils\Pagination;

class PaginationTest extends MyTestCase
{
    private $pagination;
    private $subset;

    protected function setUp()
    {
        $this->subset = [
            'subset'=>[['id'=>1,'name'=>'Record 1'],['id'=>2,'name'=>'Record 2']],
            'total'=>2,
            'limit'=>10,
            'count'=>1,
            'pos'=>0
        ];
        $this->pagination = new Pagination($this->subset);
    }

    public function testIndex()
    {
        $this->assertEquals(1, $this->pagination->index());
    }

    public function testProperties()
    {
        $this->assertEquals(2, $this->pagination->allRecordCount);
        $this->assertEquals(2, $this->pagination->recordCount);
        $this->assertEquals(1, $this->pagination->currentPage);
        $this->assertEquals(1, $this->pagination->lastPage);
        $this->assertEquals(1, $this->pagination->firstPage);
        $this->assertEquals(1, $this->pagination->first);
        $this->assertEquals(2, $this->pagination->last);
        $this->assertEquals(1, $this->pagination->counter);
        $this->assertEquals(true, $this->pagination->onFirstPage);
        $this->assertEquals(true, $this->pagination->onLastPage);
        $this->assertEquals(false, $this->pagination->prevGap);
        $this->assertEquals(false, $this->pagination->nextGap);
        $this->assertEquals(1, $this->pagination->prevPage);
        $this->assertEquals(1, $this->pagination->nextPage);
        $this->assertEquals(false, $this->pagination->hasContent);
        $this->assertEquals(1, $this->pagination->rangeStart);
        $this->assertEquals(1, $this->pagination->rangeEnd);
        $this->assertEquals($this->subset['subset'], $this->pagination->subset);
    }

    public function testGetRange()
    {
        $this->assertEquals([], $this->pagination->getRange());
    }

    public function testPath()
    {
        $base = Base::instance();
        $base['QUIET'] = true;
        $base->route('GET @paginate: /paginate', function() {
            var_dump($_GET);
        });
        $base->mock('GET /paginate');

        $this->assertStringEndsWith('/paginate?page=3', $this->pagination->path(false, 3));
    }
}
