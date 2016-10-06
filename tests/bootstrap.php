<?php

require __DIR__.'/../vendor/autoload.php';

$config = __DIR__.'/config.ini';
$base = Base::instance();

$base->config($config);
