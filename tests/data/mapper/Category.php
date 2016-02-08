<?php

namespace Nutrition\Tests\Mapper;

use Nutrition\DB\SQL\AbstractMapper;

class Category extends AbstractMapper
{
	protected $filters = [
		'category_name' => 'unique,match(/^Category.+/i)'
	];
}