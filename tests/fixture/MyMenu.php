<?php

namespace Nutrition\Test\Fixture;

use Nutrition\Utils\Menu;

class MyMenu extends Menu
{
    public function getMenu($nav)
    {
        return [['route'=>'homepage','label'=>'Homepage']];
    }
}
