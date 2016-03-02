<?php

/**
 * This file is part of eghojansu/nutrition
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

namespace Nutrition\Tests;

use Nutrition;

class NutirionTest extends \PHPUnit_Framework_TestCase
{
    public function testInfo()
    {
        Nutrition::bootstrap();
        $this->assertTrue(true, 'All Nutrition method should already work');
    }
}