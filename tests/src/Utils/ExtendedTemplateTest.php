<?php

namespace Nutrition\Test\Utils;

use MyTestCase;
use Nutrition\Security\Authorization;
use Nutrition\Utils\Breadcrumb;
use Nutrition\Utils\ExtendedTemplate;
use Nutrition\Utils\FlashMessage;
use Nutrition\Utils\Route;
use Nutrition\Utils\CommonUtil;
use Nutrition\Utils\TemplateSetup;

class ExtendedTemplateTest extends MyTestCase
{
    private $template;

    protected function setUp()
    {
        $this->template = new ExtendedTemplate();
    }

    public function testFilter()
    {
        $this->assertEquals(Route::class.'::instance()->path',
            $this->template->filter('path'));
        $this->assertEquals(Route::class.'::instance()->build',
            $this->template->filter('route'));
        $this->assertEquals(FlashMessage::class.'::instance()->get',
            $this->template->filter('flash_get'));
        $this->assertEquals(FlashMessage::class.'::instance()->all',
            $this->template->filter('flash_all'));
        $this->assertEquals(TemplateSetup::class.'::instance()->get',
            $this->template->filter('setup_get'));
        $this->assertEquals(TemplateSetup::class.'::instance()->set',
            $this->template->filter('setup_set'));
        $this->assertEquals(TemplateSetup::class.'::instance()->addPrefix',
            $this->template->filter('setup_prefix'));
        $this->assertEquals(TemplateSetup::class.'::instance()->addSuffix',
            $this->template->filter('setup_suffix'));
        $this->assertEquals(Breadcrumb::class.'::instance()->isEmpty',
            $this->template->filter('breadcrumb_empty'));
        $this->assertEquals(Breadcrumb::class.'::instance()->getContent',
            $this->template->filter('breadcrumb_content'));
        $this->assertEquals(Breadcrumb::class.'::instance()->add',
            $this->template->filter('breadcrumb_add'));
        $this->assertEquals(Breadcrumb::class.'::instance()->addCurrentRoute',
            $this->template->filter('breadcrumb_current'));
        $this->assertEquals(Breadcrumb::class.'::instance()->addGroup',
            $this->template->filter('breadcrumb_group'));
        $this->assertEquals(Breadcrumb::class.'::instance()->setRoot',
            $this->template->filter('breadcrumb_root'));
        $this->assertEquals(Breadcrumb::class.'::instance()->isLast',
            $this->template->filter('breadcrumb_last'));
        $this->assertEquals(Authorization::class.'::instance()->isGranted',
            $this->template->filter('is_granted'));
        $this->assertEquals(CommonUtil::class.'::decide',
            $this->template->filter('decide'));
        $this->assertEquals(CommonUtil::class.'::dateSQL',
            $this->template->filter('date_sql'));
        $this->assertEquals(CommonUtil::class.'::trueFalse',
            $this->template->filter('true_false'));
        $this->assertEquals(CommonUtil::class.'::onOff',
            $this->template->filter('on_off'));
        $this->assertEquals(CommonUtil::class.'::yesNo',
            $this->template->filter('yes_no'));
        $this->assertEquals(CommonUtil::class.'::postValue',
            $this->template->filter('post_value'));
        $this->assertEquals(CommonUtil::class.'::startsWith',
            $this->template->filter('starts_with'));
        $this->assertEquals(CommonUtil::class.'::endsWith',
            $this->template->filter('ends_with'));
        $this->assertEquals(CommonUtil::class.'::length',
            $this->template->filter('length'));
        $this->assertEquals(CommonUtil::class.'::random',
            $this->template->filter('random'));
        $this->assertEquals(CommonUtil::class.'::lowerLabel',
            $this->template->filter('lower_label'));
        $this->assertEquals(CommonUtil::class.'::titleCase',
            $this->template->filter('title_case'));
        $this->assertEquals(CommonUtil::class.'::snakeCase',
            $this->template->filter('snake_case'));
        $this->assertEquals(CommonUtil::class.'::pascalCase',
            $this->template->filter('pascal_case'));
        $this->assertEquals(CommonUtil::class.'::camelCase',
            $this->template->filter('camel_case'));
        $this->assertEquals(CommonUtil::class.'::dump',
            $this->template->filter('dump'));
        $this->assertEquals('ucfirst',
            $this->template->filter('capitalize'));
        $this->assertEquals('strtoupper',
            $this->template->filter('upper_case'));
        $this->assertEquals('strtolower',
            $this->template->filter('lower_case'));
    }
}
