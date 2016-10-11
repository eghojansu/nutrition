<?php

namespace Nutrition\HTML;

use Nutrition\Url;

class Navbar extends AbstractHTML
{
    /**
     * Construct ul bootstrap navbar structure
     * Only support 2-level list
     * @param  array  $items
     *         [
     *             'route'=>'routename or path',
     *             'label'=>'Path',
     *             'items'=>[
     *                 [
     *                     'path'=>'route or path',
     *                     'label'=>'Path',
     *                 ]
     *             ],
     *         ]
     * @param  string $currentPath
     * @param  array  $option @see source
     * @return string
     */
    public function render(array $items, $currentPath = null, array $option = [])
    {
        $option = array_replace_recursive([
            // ul class
            'class' => 'nav navbar-nav',
            // append ul class
            'appendClass' => '',
            // ul > li attr
            'parentAttr' => [
                'class' => 'dropdown',
            ],
            // ul > li > a attr
            'parentItemAttr' => [
                'class' => 'dropdown-toggle',
                'data-toggle' => 'dropdown',
                'role' => 'button',
                'aria-haspopup' => 'true',
                'aria-expanded' => 'false',
            ],
            // ul > li > ul attr
            'childGroupAttr' => [
                'class' => 'dropdown-menu',
            ],
            // ul > li > ul > li
            'childAttr' => [
            ],
            // ul > li > ul > li > a
            'childItemAttr' => [
            ],
            'useCaret'=>true,
        ], $option);

        $url = Url::instance();
        $str = '';
        foreach ($items as $item) {
            $item += [
                'route' => null,
                'label' => null,
                'items' => [],
            ];
            $list = '';
            $strChild = '';
            $active = $currentPath === $item['route'];
            $parentAttr = [];
            $parentItemAttr = [];
            $childGroupAttr = $option['childGroupAttr'];
            $childAttr = $option['childAttr'];
            $childItemAttr = $option['childItemAttr'];
            $childCounter = 0;

            if (count($item['items'])) {
                $activeFromChild = false;
                foreach ($item['items'] as $child) {
                    $child += [
                        'route' => null,
                        'label' => null,
                    ];

                    $childCounter++;
                    $childActive = $currentPath === $child['route'];
                    if (!$activeFromChild) {
                        $activeFromChild = $childActive;
                        $active = $activeFromChild;
                    }
                    $href = $url->path($child['route']);
                    $strChild .= $this->element('li',
                        $this->element('a', $child['label'], ['href'=>$href]+$childItemAttr),
                            $this->mergeAttributes($childAttr, ['class'=>$childActive?'active':'']));
                }
                if ($childCounter) {
                    $parentAttr += $option['parentAttr'];
                    $parentItemAttr += $option['parentItemAttr'];
                    $strChild = $this->element('ul', $strChild, $childGroupAttr);
                    if ($option['useCaret']) {
                        $item['label'] .= ' <span class="caret"></span>';
                    }
                } else {
                  $strChild = '';
                }
            }

            if (count($item['items']) && 0 === $childCounter) {
                continue;
            }
            $href = $url->path($item['route']);
            $str .= $this->element('li',
                $this->element('a', $item['label'], ['href'=>$href]+$parentItemAttr).' '.$strChild,
                    $this->mergeAttributes($parentAttr, ['class'=>$active?'active':'']));
        }
        $str = $this->element('ul', $str, $this->mergeAttributes(['class'=>$option['class']], ['class'=>$option['appendClass']]));

        return $str;
    }
}
