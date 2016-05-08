<?php

/**
 * This file is part of eghojansu/nutrition
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

namespace Nutrition\Tests\DB\SQL;

use Nutrition\DB\SQL\CreateTableParser;
use Base;

class CreateTableParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $schema = __DIR__.'/../../../data/create-table.sql';
        $sql = Base::instance()->read($schema);
        $parser = new CreateTableParser;
        $tables = $parser->parse($sql);

        $this->assertEquals(2, count($tables));
        $this->assertTrue(isset($tables['category']));
        $this->assertEquals(3, count($tables['category']));
        $this->assertEquals(8, count($tables['product']));
    }
}