<?php

namespace Nutrition\Test\Utils;

use MyTestCase;
use Nutrition\Utils\GroupChecker;

class GroupCheckerTest extends MyTestCase
{
    const GONE = 'one';
    const GTWO = 'two';

    private $groupChecker;
    private $groups = [
        'One' => self::GONE,
        'Two' => self::GTWO,
    ];

    protected function setUp()
    {
        $this->groupChecker = new GroupChecker($this->groups);
    }

    public function testGetGroup()
    {
        $this->assertEquals(self::GONE, $this->groupChecker->getGroup());
    }

    public function testGetGroups()
    {
        $this->assertEquals($this->groups, $this->groupChecker->getGroups());
    }

    public function testIsInvalid()
    {
        $this->assertFalse($this->groupChecker->isInvalid());
    }

    public function testIsValid()
    {
        $this->assertTrue($this->groupChecker->isValid());
    }

    public function testIsEqual()
    {
        $this->assertTrue($this->groupChecker->isEqual(self::GONE));
    }

    public function testIsFirst()
    {
        $this->assertTrue($this->groupChecker->isFirst());
    }

    public function testIsLast()
    {
        $this->assertFalse($this->groupChecker->isLast());
    }

    public function testGetNext()
    {
        $this->assertEquals(self::GTWO, $this->groupChecker->getNext());
    }

    public function testGetPrev()
    {
        $this->assertEquals(self::GONE, $this->groupChecker->getPrev());
    }
}
