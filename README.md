# Nutrition

Add nutrition to [fatfree framework](https://github.com/bcosca/fatfree)

## Instalation

```
composer require eghojansu/nutrition
```

## Usage

```php
<?php

require __DIR__.'/vendor/autoload.php';

// assign vars to fatfree globals var
$config = [
	'DEBUG'=>3,
	'other_var'=>'other value',
];
// assign vars via ini file
$configFilepath = __DIR__.'/app/config.ini';

// Get fatfree\Base object
$app = Nutrition::bootstrap($config, $configFilepath);

// listening 
$app->run();
```

## Create Model

Before creating model you need setting database configuration like below:

```php
<?php

$database_config = [
	'driver'=> 'mysql', // 
    'server'=> 'localhost',
    'dbname'=> 'dbname',
    'username'=> 'root',
    'password'=> 'p455w0rd',
    // 'port'=> '3306',
    // 'charset'=> 'UTF8',
    // 'socket'=> 'null',
    // for sqlite
    // 'file'=> '/path/to/database.file',
];

$app = Nutrition::bootstrap();
$app->set('DATABASE.default', $database_config);
$app->run();
```

then you can define your models,

```php
<?php

namespace app\model;

use Nutrition\DB\SQL\AbstractMapper;

class Product extends AbstractMapper
{}
```

## Controller

```php
<?php

namespace app\controller;

use Nutrition\AbstractController;

class Controller extends AbstractController
{
	public function home($app, $params)
	{
		$this->render('view.htm');
	}
}
```

## Documentation

See @source :)