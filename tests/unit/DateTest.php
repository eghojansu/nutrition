<?php

/**
 * This file is part of eghojansu/nutrition
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

namespace Nutrition\Tests;

use Nutrition\Date;

class DateTest extends \PHPUnit_Framework_TestCase
{
    public function testMonths()
    {
        $this->assertEquals(12, count(Date::months()));
    }

    public function testDays()
    {
        $this->assertEquals(7, count(Date::days()));
    }

    public function testGreetings()
    {
        $this->assertTrue(true, 'This function should already work');
    }

    public function testDayName()
    {
        $this->assertEquals('Senin', Date::dayName(1));
        $this->assertEquals('Minggu', Date::dayName('2016-05-08'));
    }

    public function testMonthName()
    {
        $this->assertEquals('Januari', Date::monthName(1));
        $this->assertEquals('Mei', Date::monthName('2016-05-08'));
    }

    public function testReadDate()
    {
        $this->assertEquals('8 Mei 2016', Date::readDate('2016-05-08'));
    }
}