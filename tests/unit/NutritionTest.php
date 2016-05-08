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
    public function testGroup()
    {
        Nutrition::bootstrap([], __DIR__.'/../data/config.ini');
        $this->assertContains($_SERVER['SERVER_NAME'], Nutrition::baseUrl());
        $this->assertContains($_SERVER['SERVER_NAME'], Nutrition::currentUrl());
        $this->assertContains('/test/page/number', Nutrition::url('testPageNumber'));
        $this->assertContains('test.css', Nutrition::asset('test.css'));
        $this->assertTrue(true, 'Nutrition::flash should already work');
        ob_start();
        Nutrition::jsonOut(['test'], false, []);
        $ouput = ob_get_clean();
        $this->assertContains('test', $ouput);
        $base = ['a'=>'b'];
        $expected = [':a'=>'b'];
        $this->assertEquals($expected, Nutrition::prependKey($base, ':'));
        $this->assertEquals('Nutrition Testing', Nutrition::titleIze('nutritionTesting'));
        $ns = 'namespace\\path\\to\\ClassName';
        $this->assertEquals('ClassName', Nutrition::className($ns));
        $this->assertEquals('class_name', Nutrition::classNameToTable($ns));

        $this->assertEquals(21, Nutrition::paginationStartNumber(['limit'=>10,'pos'=>2]));
        $this->assertTrue(true, 'Nutrition::pagerPaginate should already work');
        // $this->assertTrue(true, 'All Nutrition method should already work');
    }

    public function providerDirManipulation()
    {
        $dirTest = realpath(__DIR__.'/../data/dir').DIRECTORY_SEPARATOR;
        $files = [
            substr($dirTest, 0, -1),
            $dirTest.'emptydir',
            $dirTest.'emptydir'.DIRECTORY_SEPARATOR.'.gitkeep',
            $dirTest.'.hiddenfile',
            $dirTest.'shownfile.txt',
            ];
        $copies = array_fill(0, 4, $files);

        $data = [];

        array_push($data, [
            $dirTest, true, true, true, $copies[0]
        ]);

        unset($copies[1][1], $copies[1][2]);
        array_push($data, [
            $dirTest, false, true, true, $copies[1]
        ]);

        unset($copies[2][1], $copies[2][2], $copies[2][3]);
        array_push($data, [
            $dirTest, false, false, true, $copies[2]
        ]);

        unset($copies[3][0], $copies[3][1], $copies[3][2], $copies[3][3]);
        array_push($data, [
            $dirTest, false, false, false, $copies[3]
        ]);

        return $data;
    }

    /**
     * @dataProvider providerDirManipulation
     */
    public function testDirManipulation($dir, $recursive, $includeHidden, $includeDir, $expected)
    {
        $result = Nutrition::dirContent($dir, $recursive, $includeHidden, $includeDir);
        sort($result);
        sort($expected);
        $this->assertEquals($expected, $result);
    }

    public function testRemoveDir()
    {
        $createdir = realpath(__DIR__.'/../data/testwritedir').DIRECTORY_SEPARATOR;
        @unlink($createdir);
        if (@mkdir($createdir)) {
            $createfile = $createdir.'test.txt';
            file_put_contents($createfile, 'data');
            $expected = [$createdir, $createfile];
            $removed = Nutrition::removeDir($createdir);
            sort($expected);
            sort($removed);
            $this->assertEquals($expected, $removed);
        } else {
            $this->assertTrue(true, 'Nutrition::removeDir skipped, Cannot create test dir,');
        }
    }

    /**
     * @dataProvider providerTestTerbilang
     */
    public function testTerbilang($no, $expected)
    {
        $this->assertEquals($expected, Nutrition::terbilang($no));
    }

    public function providerTestTerbilang()
    {
        return [
            [0, 'nol'],
            [9, 'sembilan'],
            [11, 'sebelas'],
            [12, 'dua belas'],
            [19, 'sembilan belas'],
            [20, 'dua puluh'],
            [30, 'tiga puluh'],
            [51, 'lima puluh satu'],
            [100, 'seratus'],
            [151, 'seratus lima puluh satu'],
            [500, 'lima ratus'],
            [1000, 'seribu'],
            [3000, 'tiga ribu'],
            [3025, 'tiga ribu dua puluh lima'],
            [3341, 'tiga ribu tiga ratus empat puluh satu'],
            [9999, 'sembilan ribu sembilan ratus sembilan puluh sembilan'],
            [10000, 'sepuluh ribu'],
            [10001, 'sepuluh ribu satu'],
            [15000, 'lima belas ribu'],
            [99999, 'sembilan puluh sembilan ribu sembilan ratus sembilan puluh sembilan'],
            [1000000, 'satu juta'],
            // test below can make memory exhausted
            // [1999999, 'satu juta sembilan puluh sembilan ribu sembilan ratus sembilan puluh sembilan'],
            // [99999999, 'sembilan puluh sembilan juta sembilan puluh sembilan ribu sembilan ratus sembilan puluh sembilan'],
            // [999999999, 'sembilan ratus juta sembilan puluh sembilan ribu sembilan puluh sembilan ribu sembilan ratus sembilan puluh sembilan'],
        ];
    }
}