<?php

namespace Nutrition\Test\Utils;

use MyTestCase;
use Nutrition\Test\Fixture\FileSystemHelper;
use Nutrition\Utils\FileSystem;

class FileSystemTest extends MyTestCase
{
    private $dir;
    private $fs;

    protected function setUp()
    {
        $this->dir = FileSystemHelper::prepareDir();
        $this->fs = new FileSystem($this->dir);
    }

    protected function tearDown()
    {
        $this->fs->removeDir();
    }

    public function testRemoveDir()
    {
        $this->fs->removeDir(true);
        $this->assertFalse(is_dir($this->dir));
    }

    public function testRemoveFile()
    {
        $file = $this->dir.'/file.txt';
        touch($file);
        $this->assertTrue(file_exists($file));
        $this->fs->removeFile('/file.txt');
        $this->assertFalse(file_exists($file));
    }

    public function testGetContents()
    {
        $file = $this->dir.'/file.txt';
        touch($file);
        $this->assertEquals([$file], $this->fs->getContents());
    }

    public function testRealpath()
    {
        $this->assertEquals(realpath($this->dir), $this->fs->getRealpath());
    }
}
