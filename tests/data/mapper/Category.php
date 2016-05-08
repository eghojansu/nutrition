<?php

/**
 * This file is part of eghojansu/nutrition
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

namespace Nutrition\Tests\data\mapper;

use Nutrition\DB\SQL\AbstractMapper;

class Category extends AbstractMapper
{
	protected $rules = [
		'category_name' => 'unique,match(/^Category.+/i)'
	];

    protected $relations = [
        'parents' => [
            'category' => 'Category->category_id::parent_id',
        ],
        'children' => [
            'product' => 'Product->category_id::category_id',
        ],
    ];
}