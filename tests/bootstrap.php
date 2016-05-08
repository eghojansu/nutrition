<?php

/**
 * This file is part of eghojansu/nutrition
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

require __DIR__.'/../vendor/autoload.php';

$config = __DIR__.'/data/config.ini';
$schema = __DIR__.'/data/create-table.sql';

$app = Nutrition::bootstrap(['DEBUG'=>3], $config);

restore_error_handler();
restore_exception_handler();

// setting up database
$sql = $app->read($schema);
$db = Nutrition\DB\SQL\Connection::getConnection('default');
$db->pdo()->exec($sql);