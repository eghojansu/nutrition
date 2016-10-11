<?php

namespace Nutrition\HTML;

use Nutrition\Url;

class Pagination extends AbstractHTML
{
    /**
     * DBCursor::paginate result
     * @var array
     */
    protected $subset;
    protected $option;
    public $counter;

    public function __construct(array $subset = [], array $option = [])
    {
        $this->setSubset($subset);
        $this->setOption($option);
    }

    public function setOption(array $option)
    {
        $this->option = $option + [
            // ul class
            'class' => 'pagination pagination-sm',
            // append class
            'appendClass' => 'pull-right',
            // route name
            'route' => null,
            // role
            'role' => 'admin',
            // route param
            'params' => [],
            // index in route param
            'var' => 'page',
            // adjacents
            'adjacents' => 2,
            // ellipsis style
            'ellipsisStyle' => 'cursor:default',
        ];

        return $this;
    }

    public function setSubset(array $subset)
    {
        $this->subset = $subset + ['pos'=>0,'count'=>0,'limit'=>10];
        $this->counter = $this->subset['pos'] * $this->subset['limit'] + 1;

        return $this;
    }

    /**
     * Generate pagination based on subset array
     * returned by Database::paginate
     * @param  array  $subset
     * @param  array  $option
     * @return string
     */
    public function render()
    {
        extract($this->option);
        $page = (int) $this->subset['pos'];
        $max = (int) $this->subset['count'];

        $page = $page > 0 ? $page+1 : 1;

        $str = '';
        if ($max > 1) {
            $isFirstPage = $page <= 1;
            $str .= '<li'.($isFirstPage?' class="disabled"':'').'>'
                 .  '<a href="'.($isFirstPage?'#':$this->paginationHref(1)).'">&laquo;</a>'
                 .  '</li>';

            $str .= '<li'.($isFirstPage?' class="disabled"':'').'>'
                 .  '<a href="'.($isFirstPage?'#':$this->paginationHref($page-1<1?1:$page-1)).'">&lsaquo;</a>'
                 .  '</li>';

            $start = ($page <= $adjacents ? 1 : $page - $adjacents);
            $end   = ($page > $max - $adjacents ? $max : $page + $adjacents);

            if ($start > 1) {
                $str .= '<li><a style="'.$ellipsisStyle.'">...</a></li>';
            }

            for($i= $start; $i <= $end; $i++) {
                $active = $i === $page;
                $str .= '<li'.($active?' class="active"':'').'>'
                     .  '<a href="'.($active?'#':$this->paginationHref($i)).'">'.$i.'</a>'
                     . '</li>';
            }

            if ($end < $max) {
                $str .= '<li><a style="'.$ellipsisStyle.'">...</a></li>';
            }

            $isMaxPage = $page >= $max;
            $str .= '<li'.($isMaxPage?' class="disabled"':'').'>'
                 .  '<a href="'.($isMaxPage?'#':$this->paginationHref($page+1<=$max?$page+1:$page)).'">&rsaquo;</a>'
                 .  '</li>';

            $str .= '<li'.($isMaxPage?' class="disabled"':'').'>'
                 .  '<a href="'.($isMaxPage?'#':$this->paginationHref($max)).'">&raquo;</a>'
                 .  '</li>';
        }

        return $this->element('ul', $str, ['class'=>$class.' '.$appendClass]);
    }

    /**
     * Pagination href, for use in self::pagination
     * @param  array  $option
     * @param  int $page
     * @return string
     */
    protected function paginationHref($page)
    {
        $params = [$this->option['var']=>$page]+$this->option['params']+($_GET?:[]);

        return Url::instance()->path($this->option['route'], $params);
    }
}
