<?php

namespace Nutrition\Tests\Mapper;

use Nutrition\DB\SQL\AbstractMapper;

class Product extends AbstractMapper
{
    protected $labels = [
        'product_name' => 'Nama Produk'
    ];
    protected $filters = [
        'description' => 'string(null,50,true)',
        'category_id' => 'lookUp(Nutrition\\Tests\\Mapper\\Category, null, true)',
    ];
}