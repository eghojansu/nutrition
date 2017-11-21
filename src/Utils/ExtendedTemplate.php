<?php

namespace Nutrition\Utils;

use Base;
use Nutrition\Security\Authorization;
use Template;

class ExtendedTemplate extends Template
{
    public function __construct()
    {
        parent::__construct();

        $this->tags .= '|expr';
        $this->filter += [
            'path'=>Route::class.'::instance()->path',
            'route'=>Route::class.'::instance()->build',
            'flash_get'=>FlashMessage::class.'::instance()->get',
            'flash_all'=>FlashMessage::class.'::instance()->all',
            'setup_get'=>TemplateSetup::class.'::instance()->get',
            'setup_set'=>TemplateSetup::class.'::instance()->set',
            'setup_prefix'=>TemplateSetup::class.'::instance()->addPrefix',
            'setup_suffix'=>TemplateSetup::class.'::instance()->addSuffix',
            'breadcrumb_empty'=>Breadcrumb::class.'::instance()->isEmpty',
            'breadcrumb_content'=>Breadcrumb::class.'::instance()->getContent',
            'breadcrumb_add'=>Breadcrumb::class.'::instance()->add',
            'breadcrumb_current'=>Breadcrumb::class.'::instance()->addCurrentRoute',
            'breadcrumb_group'=>Breadcrumb::class.'::instance()->addGroup',
            'breadcrumb_root'=>Breadcrumb::class.'::instance()->setRoot',
            'breadcrumb_last'=>Breadcrumb::class.'::instance()->isLast',
            'is_granted'=>Authorization::class.'::instance()->isGranted',
            'decide'=>CommonUtil::class.'::decide',
            'date_sql'=>CommonUtil::class.'::dateSQL',
            'true_false'=>CommonUtil::class.'::trueFalse',
            'on_off'=>CommonUtil::class.'::onOff',
            'yes_no'=>CommonUtil::class.'::yesNo',
            'post_value'=>CommonUtil::class.'::postValue',
            'starts_with'=>CommonUtil::class.'::startsWith',
            'ends_with'=>CommonUtil::class.'::endsWith',
            'length'=>CommonUtil::class.'::length',
            'random'=>CommonUtil::class.'::random',
            'lower_label'=>CommonUtil::class.'::lowerLabel',
            'dump'=>CommonUtil::class.'::dump',
            'snake_case'=>CommonUtil::class.'::snakeCase',
            'pascal_case'=>CommonUtil::class.'::pascalCase',
            'camel_case'=>CommonUtil::class.'::camelCase',
            'title_case'=>CommonUtil::class.'::titleCase',
            'capitalize'=>'ucfirst',
            'upper_case'=>'strtoupper',
            'lower_case'=>'strtolower',
        ];
    }

    protected function _expr(array $node)
    {
        $attrib = $node['@attrib'];
        unset($node['@attrib']);

        return '<?php '.$this->token($attrib['expr']).' ?>';
    }

    /**
    *   Convert token to variable
    *   @return string
    *   @param $str string
    **/
    public function token($str) {
        $str=trim(preg_replace('/\{\{(.+?)\}\}/s',trim('\1'),
            Base::instance()->compile($str)));
        if (preg_match('/^(.+)(?<!\|)\|((?:\h*\w+(?:\h*[,;]?))+)$/s',
            $str,$parts)) {
            $str=trim($parts[1]);
            foreach (Base::instance()->split($parts[2]) as $func)
                $str=is_string($cmd=$this->filter($func))?
                    $cmd.'('.$str.')':
                    'Base::instance()->'.
                    'call($this->filter(\''.$func.'\'),['.$str.'])';
        } elseif (false !== strpos($str, '(')) {
            for ($i=0,$len=strlen($str),$newStr='',$tmp='';$i<$len;) {
                if ($str[$i] === '(') {
                    if ($tmp && array_key_exists($tmp, $this->filter)) {
                        $tmp = $this->filter[$tmp];
                    }
                    $newStr .= $tmp . '(';
                    $tmp = '';
                } elseif (in_array($str[$i], [' '])) {
                    $newStr .= $tmp . $str[$i];
                    $tmp = '';
                } else {
                    $tmp .= $str[$i];
                }
                $i++;
            }
            $newStr .= $tmp;
            $str = $newStr;
        }

        return $str;
    }
}
