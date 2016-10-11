<?php

namespace Nutrition\HTML;

use Nutrition\Url;

/**
 * Generate breadcrumb
 */
class Breadcrumb extends AbstractHTML
{
    protected $urls = [];

    /**
     * Add breadcrumb
     * @param string $label
     * @param string $link
     * @param array  $args
     */
    public function add($label, $link = null, array $args = [])
    {
        $this->urls[] = ['label'=>$label,'link'=>$link,'args'=>$args];

        return $this;
    }

    /**
     * Remove item
     * @param  int $index
     */
    public function remove($index)
    {
        unset($this->urls[$index]);

        return $this;
    }

    /**
     * Render breadcrumb
     * @param  array  $options
     * @return string
     */
    public function render(array $options = [])
    {
        $li = '';
        $urlClass = Url::instance();
        $urls = $this->urls;
        $last = array_pop($urls);
        foreach ($urls as $key => $url) {
            $li .= $this->element('li', $this->element('a', $url['label'], [
                'href'=>$urlClass->path($url['link'], $url['args']),
                ]));
        }
        $li .= $this->element('li', $last['label'], ['class'=>'active']);

        $options += [
            'class'=>'breadcrumb',
        ];

        return $this->element('ul', $li, $options);
    }

    /**
     * Reverse Render breadcrumb
     * @param  array  $options
     * @return string
     */
    public function renderReverse(array $options = [])
    {
        $li = '';
        $urlClass = Url::instance();
        $urls = $this->urls;
        krsort($urls);
        $first = array_shift($urls);
        $li .= $this->element('li', $first['label'], ['class'=>'active']);
        foreach ($urls as $key => $url) {
            $li .= $this->element('li', $this->element('a', $url['label'], [
                'href'=>$urlClass->path($url['link'], $url['args']),
                ]));
        }

        $options += [
            'class'=>'breadcrumb',
        ];

        return $this->element('ul', $li, $options);
    }
}
