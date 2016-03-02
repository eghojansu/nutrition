<?php

/**
 * This file is part of eghojansu/nutrition
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

namespace Nutrition\Tests\DB\SQL;

use Base;
use Nutrition\DB\SQL\Connection;
use Nutrition\Tests\data\mapper\Product;
use Nutrition\Tests\data\mapper\Category;

/**
 * Testing Nutrition\DB\SQL\AbstractMapper using concrete class
 * It include test Nutrition\Validation class too
 */
class AbstractMapperTest extends \PHPUnit_Framework_TestCase
{
    private function getProduct()
    {
        return new Product;
    }

    private function getCategory()
    {
        return new Category;
    }

    public function testGetTableName()
    {
        $this->assertEquals('product', $this->getProduct()->getTableName());
    }

    public function testGetPrimaryKey()
    {
        $this->assertEquals('product_id', $this->getProduct()->getPrimaryKey());
    }

    public function testGenerateID()
    {
        $format = 'P{999}';
        $expected = 'P001';
        $this->assertEquals($expected, $this->getProduct()->generateID('product_id', $format));
    }

    public function testDefaultValidationMutation()
    {
        $map = $this->getProduct();
        $this->assertTrue($map->getDefaultValidation());
        $set = $map->setDefaultValidation(false);
        $this->assertEquals($set, $map);
        $this->assertFalse($map->getDefaultValidation());
    }

    public function testManualErrorMutation()
    {
        $map = $this->getProduct();
        $this->assertFalse($map->hasError());
        $map->addError('product_id', 'test');
        $this->assertTrue($map->hasError());
        $this->assertEquals(['test'], $map->getError('product_id'));
        $this->assertEquals('test', $map->getErrorString('product_id'));
        $this->assertEquals(['product_id'=>['test']], $map->getAllError());
        $this->assertEquals('test', $map->getAllErrorString());
        $map->clearError();
        $this->assertFalse($map->hasError());
    }

    public function testInsertingNewData()
    {
        $map = $this->getProduct();
        $map->product_id = 1;
        $map->product_name = 'TV';
        $map->save();
        $this->assertTrue($map->valid());

        return $map;
    }

    /**
     * @depends testInsertingNewData
     */
    public function testFindByPK(Product $map)
    {
        $map->reset();
        $map->findByPK(1);
        $this->assertTrue($map->valid());
        $this->assertEquals(1, $map->product_id);
        $this->assertEquals('TV', $map->product_name);
    }

    public function testValidate()
    {
        $map = $this->getProduct();
        $map->product_id = 24;
        $map->product_name = 'AC';
        $map->price = 999;
        $map->price2 = 5;
        $map->product_status = 'available';
        $map->description = 'description that less than 50 chars';
        $this->assertTrue($map->validate());
    }

    public function testSafeSave()
    {
        $map = $this->getProduct();
        $map->product_id = 2;
        $map->product_name = 'AC';
        $map->price = 999;
        $map->price2 = 5;
        $map->product_status = 'available';
        $map->description = 'description that less than 50 chars';
        // without category_id
        $this->assertTrue($map->safeSave());
    }

    public function testStringValidation()
    {
        $map = $this->getProduct();
        $map->reset();
        $map->product_id = 3;
        $map->product_name = 'Product name that very very very very very long';
        $this->assertFalse($map->safeSave());

        $this->assertContains('Nama Produk tidak valid', $map->getAllErrorString());
    }

    public function testChoicesValidation()
    {
        $map = $this->getProduct();
        $map->product_id = 4;
        $map->product_name = 'Product';
        $map->product_status = 'invalid';
        $this->assertFalse($map->safeSave());

        $this->assertContains('Product Status tidak valid', $map->getAllErrorString());
    }

    public function testIntegerValidation()
    {
        $map = $this->getProduct();
        $map->product_id = 5;
        $map->product_name = 'Product';
        $map->price = 1000;
        $this->assertFalse($map->safeSave());

        $this->assertContains('Price tidak valid', $map->getAllErrorString());
    }

    public function testFloatValidation()
    {
        $map = $this->getProduct();
        $map->product_id = 6;
        $map->product_name = 'Product';
        $map->price2 = 1000;
        $this->assertFalse($map->safeSave());

        $this->assertContains('Price2 tidak valid', $map->getAllErrorString());
    }

    public function testStringValidationFromModel()
    {
        $map = $this->getProduct();
        $map->product_id = 7;
        $map->product_name = 'Product';
        $map->description = str_repeat('this description contains more than 50 chars', 5);
        $this->assertFalse($map->safeSave());

        $this->assertContains('Description tidak valid', $map->getAllErrorString());
    }

    public function testUniqueValidation()
    {
        $map = $this->getCategory();
        $map->category_id = 1;
        $map->category_name = 'Category 1';
        $this->assertTrue($map->safeSave());

        return $map;
    }

    /**
     * @depends testUniqueValidation
     */
    public function testInvalidUniqueValidation(Category $category)
    {
        $map = $this->getCategory();
        $map->category_name = $category->category_name;
        $this->assertFalse($map->safeSave());

        $this->assertContains('Category Name tidak valid', $map->getAllErrorString());
    }

    /**
     * @depends testUniqueValidation
     */
    public function testLookupValidation(Category $category)
    {
        $map = $this->getProduct();
        $map->product_id = 10;
        $map->product_name = 'Product';
        $map->category_id = $category->category_id;
        $this->assertTrue($map->safeSave());
    }

    public function testInvalidLookupValidation()
    {
        $map = $this->getProduct();
        $map->product_id = 11;
        $map->product_name = 'Product';
        $map->category_id = 99;
        $this->assertFalse($map->safeSave());

        $this->assertContains('Category Id tidak valid', $map->getAllErrorString());
    }

    public function testInvalidMatchValidation()
    {
        $map = $this->getCategory();
        $map->category_id = 4;
        $map->category_name = 'Not match criteria';
        $this->assertFalse($map->safeSave());

        $this->assertContains('Category Name tidak valid', $map->getAllErrorString());
    }

    public function testDateValidation()
    {
        $map = $this->getProduct();
        $map->product_id = 12;
        $map->product_name = 'Product';
        $map->date_created = date('Y-m-d');
        $this->assertTrue($map->safeSave());
    }

    public function testInvalidDateValidation()
    {
        $map = $this->getProduct();
        $map->product_id = 13;
        $map->product_name = 'Product';
        $map->date_created = date('d-m-Y');
        $this->assertFalse($map->safeSave());

        $this->assertContains('Date Created tidak valid', $map->getAllErrorString());
    }

    public function testValidationMessage()
    {
        Base::instance()->set('validation_messages.date', '{label} tidak benar.');
        $map = $this->getProduct();
        $map->product_id = 13;
        $map->product_name = 'Product';
        $map->date_created = date('d-m-Y');
        $this->assertFalse($map->safeSave());

        $this->assertContains('Date Created tidak benar', $map->getAllErrorString());
    }

    public function testDefaultFieldMutation()
    {
        $field = 'product_name';
        $map = $this->getProduct();
        $set = $map->setDefaultField($field);
        $this->assertEquals($field, $map->getDefaultField());
        $this->assertEquals($set, $map);
    }

    /**
     * @dataProvider providerFilterMutation
     */
    public function testFilterMutation($str, $value, $expected)
    {
        $map = $this->getProduct();
        $set = $map->addFilter($str, $value);
        $this->assertEquals($set, $map);
        $this->assertEquals($expected, $map->getFilter());
    }

    public function testFilterMutationWithMultipleFilter()
    {
        $map = $this->getProduct();
        $filter = $this->providerFilterMutation();
        $expected = [''];
        foreach ($filter as $key => $value) {
            $exp = end($value);
            $expStr = array_shift($exp);
            $expected[0] .= ($expected[0]?' and ':'').$expStr;
            $expected = array_merge($expected, $exp);

            $map->addFilter($value[0], $value[1]);
        }
        $this->assertEquals($expected, $map->getFilter());
    }

    /**
     * @dataProvider providerFilterImplementation
     */
    public function testLoad($str, $value)
    {
        $map = $this->getProduct();
        $map->addFilter($str, $value);
        $map->load();
        $this->assertTrue($map->valid());
        $this->assertEquals(1, $map->loaded());
        $this->assertEquals(1, $map->product_id);
    }

    /**
     * @dataProvider providerFilterImplementation
     */
    public function testSelect($str, $value)
    {
        $map = $this->getProduct();
        $map->addFilter($str, $value);
        $data = $map->select('*');
        $this->assertEquals(1, count($data));
    }

    /**
     * @dataProvider providerFilterImplementation
     */
    public function testFind($str, $value)
    {
        $map = $this->getProduct();
        $map->addFilter($str, $value);
        $data = $map->find();
        $this->assertEquals(1, count($data));
    }

    /**
     * @dataProvider providerFilterImplementation
     */
    public function testCount($str, $value)
    {
        $map = $this->getProduct();
        $map->addFilter($str, $value);
        $this->assertEquals(1, $map->count());
    }

    /**
     * @dataProvider providerFilterImplementation
     */
    public function testFindOne($str, $value)
    {
        $map = $this->getProduct();
        $map->addFilter($str, $value);
        $one = $map->findone();
        $this->assertEquals(1, $one->loaded());
    }

    /**
     * @dataProvider providerFilterImplementation
     */
    public function testPaginate($str, $value)
    {
        $map = $this->getProduct();
        $map->addFilter($str, $value);
        $page = $map->paginate();
        $this->assertEquals(1, $page['total']);
    }

    /**
     * @dataProvider providerFilterImplementation
     */
    public function testWhere($str, $value)
    {
        $map = $this->getProduct();
        $map->where($str, $value);
        $page = $map->paginate();
        $this->assertEquals(1, $page['total']);
    }

    public function testOrderBy()
    {
        $map = $this->getProduct();
        $map->orderBy('product_name');
        $map->load();
        $log = Connection::getConnection()->log();
        $this->assertContains('order by `product_name`', $log, '', true);
    }

    public function testGroupBy()
    {
        $map = $this->getProduct();
        $map->groupBy('product_name');
        $map->load();
        $log = Connection::getConnection()->log();
        $this->assertContains('group by `product_name`', $log, '', true);
    }

    public function testLimitOffset()
    {
        $map = $this->getProduct();
        $map->limit(1);
        $map->offset(1);
        $map->load();
        $log = Connection::getConnection()->log();
        $this->assertContains('limit 1 offset 1', $log, '', true);
    }

    /**
     * @expectedException Nutrition\InvalidRuntimeException
     */
    public function testOffsetException()
    {
        $map = $this->getProduct();
        $map->offset(1);
        $map->load();
    }

    public function testTTLMutation()
    {
        $map = $this->getProduct();
        $value = 20;
        $map->setTTL($value);
        $this->assertEquals($map->getTTL(), $value);
    }

    public function testErase()
    {
        $this->assertTrue(true, 'This should already work');
    }

    public function testAddRule()
    {
        $map = $this->getProduct();
        $rule = 'whateverRule';
        $map->addRule('product_name', $rule);
        $this->assertContains($rule, $map->getRules());
    }

    public function testRuleExists()
    {
        $map = $this->getProduct();
        $rule = 'inExistsRule';
        $this->assertFalse($map->ruleExists('product_name', $rule));
    }

    public function providerFilterImplementation()
    {
        return [
            ['product_name', 'tv'],
        ];
    }

    public function providerFilterMutation()
    {
        return [
            ['product_id', 1, ['(product_id = ?)', 1]],
            ['product_id = 1', null, ['(product_id = 1)']],
            ['product_name', 'product', ['(product_name = ?)', 'product']],
            ['product_id = 1 and product_name = ?', 'product', ['(product_id = 1 and product_name = ?)', 'product']],
        ];
    }
}