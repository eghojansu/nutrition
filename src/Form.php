<?php

/**
 * This file is part of eghojansu/nutrition
 *
 * @author Eko Kurniawan <ekokurniawanbs@gmail.com>
 */

namespace Nutrition;

use Nutrition\DB\SQL\AbstractMapper;
use Nutrition\Date;
use Base;

/**
 * Form helper
 */
class Form
{
    /**
     * @var Nutrition\DB\SQL\AbstractMapper
     */
    protected $model;
    /**
     * Scripts to include
     * @var array
     */
    protected $scripts = [];

    /**
     * @param AbstractMapper $model
     */
    public function __construct(AbstractMapper $model)
    {
        $this->model = $model;
    }

    /**
     * open form
     * @param  array  $attrs
     * @return string
     */
    public function open(array $attrs = [])
    {
        $result = '<form '.$this->compileAttributes($attrs).'>';

        return $result;
    }

    /**
     * Close form and implode script if any
     * @return string
     */
    public function close()
    {
        $script = '<scripts type="text/javascript">'
                . PHP_EOL
                . implode(PHP_EOL, $this->scripts)
                . PHP_EOL
                . '</script>';
        $result = ($this->scripts?$script.PHP_EOL:'').'</form>';

        return $result;
    }

    /**
     * Generate hidden field
     * @param  string $name
     * @param  array  $attrs
     * @return string
     */
    public function hidden($name, array $attrs = [])
    {
        $attrs = ['type'=>'hidden','placeholder'=>'']+$attrs;

        return $this->input($name, $attrs);
    }

    /**
     * Generate input
     * @param  string $name
     * @param  array  $attrs
     * @return string
     */
    public function input($name, array $attrs = [])
    {
        $value = $this->model->exists($name)?$this->model->get($name):null;
        $label = $this->model->getLabel($name);
        $attrs += [
            'type'=>'text',
            'name'=>$name,
            'value'=>$value,
            'placeholder'=>$label,
            'style'=>'',
            'class'=>''
        ];

        $result = '<input '.$this->compileAttributes($attrs).'>';

        return $result;
    }

    /**
     * Generate input radio
     * @param  string $name
     * @param  array|null $list
     * @param  array  $attrs
     * @return string
     */
    public function radio($name, $list, array $attrs = [])
    {
        $result = '';
        $attrs += [
            'type'=>'radio',
            'placeholder'=>'',
            'label'=>[],
        ];
        $labelAttrs = $this->compileAttributes($attrs['label']);
        unset($attrs['label']);
        $realValue = $this->model->exists($name)?(string) $this->model->get($name):'';

        foreach ($list?:[] as $value => $label) {
            $value = (string) $value;
            $input = $this->input($name, $attrs+[
                    'value'=>$value,
                    'checked'=>($realValue===$value?' checked':'')
                    ]);

            $result .= ($result?'&nbsp;&nbsp;&nbsp;':'').'<label '.$labelAttrs.'>'.
                $input.' '.$label.
                '</label>';
        }

        return $result;
    }

    /**
     * Take sql date to combobox date
     * @param  string $name
     * @param  array  $attrs
     * @return string
     */
    public function inputDate($name, array $attrs = [])
    {
        $result = '';
        $fw = Base::instance();
        $token = $fw->hash(microtime());
        $attrs += [
            'startYear'=>1900,
            'endYear'=>2030,
            'style-d'=>'width: 50px',
            'style-m'=>'width: 95px',
            'style-y'=>'width: 65px',
            ];
        $value = $this->model->exists($name)?$this->model->get($name):null;
        $dateSelected = array_filter(explode('-', $value))+
            [date('Y'),date('m'),date('d')];

        $defaultAttrs = [
            'onchange'=>'nds(\''.$token.'\')',
            ];

        $result .= '<select '.$this->compileAttributes($defaultAttrs+[
            'id'=>'d'.$token,
            'style'=>$attrs['style-d'],
            ]).'>';
        for ($i=1; $i < 32; $i++) {
            $result .= '<option value="'.$i.'"'.($dateSelected[2]==$i?' selected':'').'>'.$i."</option>";
        }
        $result .= '</select>';

        $result .= '<select '.$this->compileAttributes($defaultAttrs+[
            'id'=>'m'.$token,
            'style'=>$attrs['style-m'],
            ]).'>';
        foreach (Date::$months() as $no=>$bulan) {
            $result .= '<option value="'.$no.'"'.($dateSelected[1]==$no?' selected':'').'>'.$bulan."</option>";
        }
        $result .= '</select>';

        $result .= '<select '.$this->compileAttributes($defaultAttrs+[
            'id'=>'y'.$token,
            'style'=>$attrs['style-y'],
            ]).'>';
        for ($i=$attrs['endYear']; $i > $attrs['startYear']; $i--) {
            $result .= '<option value="'.$i.'"'.($dateSelected[0]==$i?' selected':'').'>'.$i."</option>";
        }
        $result .= '</select>';

        $value = implode('-', $dateSelected);
        $result .= '<input type="hidden" name="'.$name.'" value="'.$value.'" id="v'.$token.'">';

        if (!isset($this->scripts['radio'])) {
            $this->scripts['radio'] = <<<JS
<script type="text/javascript">
function nds(token) {
    var d = document.getElementById("d"+token).value;
    var m = document.getElementById("m"+token).value;
    var y = document.getElementById("y"+token).value;
    var v = y+"-"+("00"+m).slice(-2)+"-"+("00"+d).slice(-2);

    document.getElementById("v"+token).value = v;

    return true;
}
</script>
JS;
        }

        return $result;
    }

    /**
     * Dropdown
     * @param  string $name
     * @param  array|null $list
     * @param  array  $attrs
     * @return string
     */
    public function dropdown($name, $list, array $attrs = [])
    {
        $attrs += [
            'name'=>$name,
            'placeholder'=>''
        ];
        $realValue = $this->model->exists($name)?(string) $this->model->get($name):'';

        $result = '<select '.$this->compileAttributes($attrs).'>';
        foreach ($list?:[] as $value => $label) {
            $value = (string) $value;
            $result .= '<option value="'.$value.'"'.($value===$realValue?' selected':'').'>'.$label.'</option>';
        }
        $result .= '</select>';

        return $result;
    }

    /**
     * Generate textarea
     * @param  string $name
     * @param  array  $attrs
     * @return string
     */
    public function textarea($name, array $attrs = [])
    {
        $value = $this->model->exists($name)?$this->model->get($name):null;
        $label = $this->model->getLabel($name);
        $attrs += [
            'name'=>$name,
            'placeholder'=>$label,
        ];

        $result = '<textarea '.$this->compileAttributes($attrs).'>'.$value.'</textarea>';

        return $result;
    }

    /**
     * Compile array of attributes to string
     * @param  array  $attrs
     * @return string
     */
    protected function compileAttributes(array $attrs)
    {
        $result = '';
        ksort($attrs);

        $attrs = array_filter($attrs, function($var) {
            return (!is_null($var) && trim($var) !== '');
        })?:[];
        foreach ($attrs as $key => $value) {
            $result .= ($result?' ':'').(is_numeric($key)?$value:$key.'="'.$value.'"');
        }

        return $result;
    }
}