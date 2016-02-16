<?php

namespace Nutrition\Tests\data\mapper;

use Nutrition\DB\SQL\AbstractMapper;

class Category extends AbstractMapper
{
	protected $rules = [
		'category_name' => 'unique,match(/^Category.+/i)'
	];
}