<?php

/**
 * This file is part of eghojansu/nutrition
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

namespace Nutrition\Tests;

use Nutrition\Form;
use Nutrition\Tests\data\mapper\Category;

class FormTest extends \PHPUnit_Framework_TestCase
{
    public function testForm()
    {
        $categories = [
            [50, 'testc', 0],
            [51, 'testcc', 0],
            [52, 'testccc', 50],
        ];

        $model = new Category;

        $expected = '<form action="test">'
                  . '<input name="category_id" type="hidden">'
                  . '<input name="category_name" placeholder="Category Name" type="text">'
                  . '<label ><input name="category_name" type="radio" value="0"> name a</label>'
                  . '&nbsp;&nbsp;&nbsp;'
                  . '<label ><input name="category_name" type="radio" value="1"> name b</label>'
                  . '<textarea name="category_name" placeholder="Category Name"></textarea>'
                  . '<select name="category_name">'
                  . '<option value="0">Test</option>'
                  . '</select>'
                  . '</form>';

        // inputDate not tested
        $form = new Form($model);
        $str = $form->open(['action'=>'test']);
        $str .= $form->hidden('category_id');
        $str .= $form->input('category_name');
        $str .= $form->radio('category_name', ['name a', 'name b']);
        $str .= $form->textarea('category_name');
        $str .= $form->dropdown('category_name', ['Test']);
        $str .= $form->close();

        $this->assertEquals($expected, $str);
    }
}