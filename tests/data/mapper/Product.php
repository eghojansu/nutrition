<?php

namespace Nutrition\Tests\data\mapper;

use Nutrition\DB\SQL\AbstractMapper;

class Product extends AbstractMapper
{
    protected $labels = [
        'product_name' => 'Nama Produk'
    ];
    protected $rules = [
        'description' => 'string(null,50,true)',
        'category_id' => 'lookUp(Nutrition\\Tests\\data\\mapper\\Category, null, true)',
    ];
}