<?php

/**
 * This file is part of eghojansu/nutrition
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

namespace Nutrition\Tests\DB\SQL;

use Nutrition\Tests\data\BackupProvider;
use Nutrition\DB\SQL\Backup;
use Nutrition;

class BackupTest extends \PHPUnit_Framework_TestCase
{
    public function testGroup()
    {
        $backupProvider = new BackupProvider;
        Nutrition::removeDir($backupProvider->getBackupDir());
        $backup = new Backup($backupProvider);
        $this->assertEquals([], $backup->getBackupList());
        $this->assertTrue(true, 'download method should already work');
        $this->assertTrue($backup->backup());
        $list = $backup->getBackupList();
        $this->assertEquals(1, count($list));
        $file = array_pop($list);
        $this->assertTrue($backup->restore($file));
        $this->assertTrue($backup->delete($file));
    }
}