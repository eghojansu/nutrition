<?php

namespace Nutrition\HTML;

use Base;
use DB\Cursor;
use Nutrition\Helper;
use Nutrition\Validation;

class Form extends AbstractHTML
{
    /**
     * @var DB\Cursor
     */
    protected $mapper;

    /**
     * @var  Nutrition\Validation
     */
    public $validation;

    protected $attrs = [];
    protected $labels = [];
    protected $controlAttrs = [];
    protected $labelAttrs = [];
    protected $labelElement = 'label';
    protected $method = 'POST';

    /**
     * Construct
     *
     * @param DB\Cursor $mapper
     */
    public function __construct(Cursor $mapper = null)
    {
        $this->mapper = $mapper;
        $this->validation = new Validation($mapper, $this->all());
        $this->init();
    }

    /**
     * Init after construction
     */
    protected function init()
    {
        $this->validation->resolveDefaultFilter();

        return $this;
    }

    protected function onOpen()
    {
        return '';
    }

    protected function onClose()
    {
        return '';
    }

    protected function findFilter($field, array $filters = [])
    {
        $fieldFilters = $this->validation->getFilter($field);
        $rules = [];
        foreach ($filters as $filter=>$paramAs) {
            if (isset($fieldFilters[$filter])) {
                foreach ($paramAs as $key=>$name) {
                    if (isset($fieldFilters[$filter][$key])) {
                        $rules[$name] = $fieldFilters[$filter][$key];
                    }
                }
            }
        }

        return $rules;
    }

    /**
     * Get field value
     *
     * @param  string
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $key = $this->method.'.'.$name;
        $value = Base::instance()->get($key);

        return $value ?: $default;
    }

    /**
     * Get all
     *
     * @param  string
     * @return array
     */
    public function all($default = [])
    {
        $value = Base::instance()->get($this->method);

        return $value ?: $default;
    }

    /**
     * Assign data to mapper
     *
     * @param  array  $data
     * @return object $this
     */
    public function assign(array $data)
    {
        foreach ($this->mapper?$data:[] as $key => $value) {
            $this->mapper->set($key, $value);
        }

        return $this;
    }

    /**
     * Assign from request data
     *
     * @return object $this
     */
    public function assignFromRequest(array $except = [])
    {
        $all = $this->all();
        foreach ($except as $key) {
            unset($all[$key]);
        }

        return $this->assign($all);
    }

    /**
     * Open form
     * @return string
     */
    public function open()
    {
        $attrs = $this->attrs;
        $attrs['method'] = $this->method;
        $str = '<form'.$this->renderAttributes($attrs).'>'.$this->onOpen();

        return $str;
    }

    /**
     * Close form
     * @return string
     */
    public function close()
    {
        $str = $this->onClose().'</form>';

        return $str;
    }

    /**
     * Generate control element
     * @param  string  $name
     * @param  array   $attrs
     * @param  boolean $override
     * @return string
     */
    public function element($element, $name = null, array $attrs = [])
    {
        $default = [
            'value'=>$this->value($name),
        ];
        $attrs += $default;
        $str = parent::element($element, $attrs['value'], $attrs);

        return $str;
    }

    /**
     * Generate control label
     * @param  string  $name
     * @param  array   $attrs
     * @param  boolean $override
     * @return string
     */
    public function label($name, array $attrs = [], $override = false)
    {
        $default = [
            'for'=>$name,
        ];
        $attrs = ($override?$attrs:$this->mergeAttributes($this->labelAttrs, $attrs))+$default;
        $str = parent::element($this->labelElement, $this->readName($name), $attrs);

        return $str;
    }

    /**
     * Generate input control
     * @param  string  $type
     * @param  string  $name
     * @param  array   $attrs
     * @param  boolean $override
     * @return string
     */
    public function input($type, $name, array $attrs = [], $override = false)
    {
        $default = [
            'type'=>$type,
            'name'=>$name,
            'id'=>$name,
            'value'=>$this->value($name),
            'placeholder'=>$this->readName($name),
        ]+$this->findFilter($name,['required'=>['required']]);

        $attrs = ($override?$attrs:$this->mergeAttributes($this->controlAttrs, $attrs))+$default;
        $str = '<input'.$this->renderAttributes($attrs).'>';

        return $str;
    }

    /**
     * Generate text control
     * @see  input
     */
    public function text($name, array $attrs = [], $override = false)
    {
        $default = []+$this->findFilter($name,['maxLength'=>['maxlength']]);
        $str = $this->input('text', $name, $attrs+$default, $override);

        return $str;
    }

    /**
     * Generate password control
     * @see  input
     */
    public function password($name, array $attrs = [], $override = false)
    {
        $default = []+$this->findFilter($name,['maxLength'=>['maxlength']]);
        $str = $this->input('password', $name, $attrs+$default, $override);

        return $str;
    }

    /**
     * Generate number control
     * @see  input
     */
    public function number($name, array $attrs = [], $override = false)
    {
        $default = $this->findFilter($name,['maxInt'=>['max'],'minInt'=>['min']])+['min'=>1];
        $str = $this->input('number', $name, $attrs+$default, $override);

        return $str;
    }

    /**
     * Generate file control
     * @see  input
     */
    public function file($name, array $attrs = [], $override = false)
    {
        $default = [
            'value'=>null,
        ];
        $str = $this->input('file', $name, $attrs+$default, $override);

        return $str;
    }

    /**
     * Generate hidden control
     * @see  input
     */
    public function hidden($name, array $attrs = [], $override = false)
    {
        $default = ['required'=>false];
        $str = $this->input('hidden', $name, $attrs+$default, $override);

        return $str;
    }

    /**
     * Generate radio control
     * @see  input
     */
    public function radio($name, array $attrs = [], $override = true)
    {
        $nameValue = $this->value($name);
        $default = [
            'value'=>null,
            'label'=>$this->readName($name),
            'wrapLabel'=>false,
        ];
        $attrs += $default;
        if ($attrs['value'] == $nameValue) {
            $attrs[] = 'checked';
        }
        $label = $attrs['label'];
        $wrapLabel = $attrs['wrapLabel'];
        unset($attrs['label'],$attrs['wrapLabel']);
        $str = $this->input('radio', $name, $attrs, $override).' '.$label;
        if ($wrapLabel) {
            $str = '<label'.$this->renderAttributes(is_array($wrapLabel)?$wrapLabel:[]).'>'.$str.'</label>';
        }

        return $str;
    }

    /**
     * Generate checkbox control
     * @see  input
     */
    public function checkbox($name, array $attrs = [], $override = true)
    {
        $nameValue = $this->value($name);
        $default = [
            'value'=>null,
            'label'=>$this->readName($name),
            'wrapLabel'=>false,
        ];
        $attrs += $default;
        if ($attrs['value'] == $nameValue || (false !== strpos($nameValue, ',') && preg_match('/\b'.preg_quote($attrs['value']).'\b/', $nameValue))) {
            $attrs[] = 'checked';
        }
        $label = $attrs['label'];
        $wrapLabel = $attrs['wrapLabel'];
        unset($attrs['label'],$attrs['wrapLabel']);
        $str = $this->input('checkbox', $name, $attrs, $override).' '.$label;
        if ($wrapLabel) {
            $str = '<label'.$this->renderAttributes(is_array($wrapLabel)?$wrapLabel:[]).'>'.$str.'</label>';
        }

        return $str;
    }

    /**
     * Generate radio list control
     * @see  input
     */
    public function radioList($name, array $attrs = [], $override = true)
    {
        $default = [
            'name'=>$name,
            'options'=>[],
            'checked'=>$this->value($name),
            'renderer'=>null,
        ];
        $attrs += $default;
        $options = $attrs['options'];
        $checked = $attrs['checked'];
        $renderer = $attrs['renderer'];
        unset($attrs['options'],$attrs['checked'],$attrs['renderer']);

        if ($renderer && is_callable($renderer)) {
            $str = call_user_func_array($renderer, [$checked,$options]);
        } else {
            $str = '';
            foreach ($options as $label => $value) {
                $attrs['value'] = $value;
                $attrs['label'] = $label;
                $str .= $this->radio($name, $attrs, $override);
            }
        }

        return $str;
    }

    /**
     * Generate checkbox control
     * @see  input
     */
    public function checkboxList($name, array $attrs = [], $override = true)
    {
        $default = [
            'name'=>$name.'[]',
            'options'=>[],
            'checked'=>$this->value($name),
            'renderer'=>null,
        ];
        $attrs += $default;
        $options = $attrs['options'];
        $checked = $attrs['checked'];
        $renderer = $attrs['renderer'];
        unset($attrs['options'],$attrs['checked'],$attrs['renderer']);

        if ($renderer && is_callable($renderer)) {
            $str = call_user_func_array($renderer, [$checked,$options]);
        } else {
            $str = '';
            foreach ($options as $label => $value) {
                $attrs['value'] = $value;
                $attrs['label'] = $label;
                $str .= $this->checkbox($name, $attrs, $override);
            }
        }

        return $str;
    }

    /**
     * Generate combobox control
     * @see  input
     */
    public function dropdown($name, array $attrs = [], $override = false)
    {
        $default = [
            'name'=>$name,
            'options'=>[],
            'restAsData'=>false,
            'label'=>[],
            'group'=>null,
            'labelImplode'=>' - ',
            'selected'=>$this->value($name),
            'renderer'=>null,
            'placeholder'=>'-- pilih '.$this->readName($name),
        ]+$this->findFilter($name, ['required'=>['required']]);
        $attrs = ($override?$attrs:$this->mergeAttributes($this->controlAttrs, $attrs))+$default;
        $v = ['options','selected','renderer','restAsData','label','labelImplode','placeholder','group'];
        foreach ($v as $key => $value) {
            $v[$value] = $attrs[$value];
            unset($v[$key], $attrs[$value]);
        }

        if ($v['renderer'] && is_callable($v['renderer'])) {
            $optionStr = call_user_func_array($v['renderer'], [$v['selected'],$v['options']]);
        } else {
            $optionStr = '';
            if ($v['placeholder']) {
                $optionStr .= parent::element('option', $v['placeholder'], ['value'=>null]);
            }
            $prev = null;
            $tmp = '';
            foreach ($v['options'] as $label => $value) {
                if (is_array($value)) {
                    if ($v['group'] && isset($value[$v['group']]) && $value[$v['group']] != $prev) {
                        $optionStr .= $tmp?parent::element('optgroup', $tmp, ['label'=>$prev]):$tmp;
                        $tmp = '';
                        $prev = $value[$v['group']];
                    }
                    $a = ['value'=>$label];
                    $vvalue = $label;
                    if ($v['label']) {
                        $label = '';
                        foreach ($v['label'] as $key) {
                            $label .= ($label?$v['labelImplode']:'').$value[$key];
                            unset($value[$key]);
                        }
                    }
                    foreach ($v['restAsData']?$value:[] as $key => $keyvalue) {
                        $key = 'data-'.str_replace('_', '-', $key);
                        $a[$key] = $keyvalue;
                    }
                    if ($vvalue == $v['selected']) {
                        $a[] = 'selected';
                    }
                    $tmp .= parent::element('option', $label, $a);
                } else {
                    $a = ['value'=>$value];
                    if ($value == $v['selected']) {
                        $a[] = 'selected';
                    }
                    $optionStr .= parent::element('option', $label, $a);
                }
            }
            $optionStr .= $prev?parent::element('optgroup', $tmp, ['label'=>$prev]):$tmp;
        }
        $str = parent::element('select', $optionStr, $attrs);

        return $str;
    }

    /**
     * Generate textarea control
     * @see  input
     */
    public function textarea($name, array $attrs = [], $override = false)
    {
        $default = [
            'name'=>$name,
            'value'=>$this->value($name),
            'placeholder'=>$this->readName($name),
        ]+$this->findFilter($name, ['maxLength'=>['maxlength'],'required'=>['required']]);
        $attrs = ($override?$attrs:$this->mergeAttributes($this->controlAttrs, $attrs))+$default;
        $value = $attrs['value'];
        unset($attrs['value']);
        $str = '<textarea'.$this->renderAttributes($attrs).'>'.$value.'</textarea>';

        return $str;
    }

    /**
     * Generate month list control
     * @see  input
     */
    public function monthList($name, array $attrs = [], $override = false)
    {
        $default = [
            'options'=>array_flip(Helper::$months),
        ];
        $attrs += $default;
        $str = $this->dropdown($name, $attrs, $override);

        return $str;
    }

    /**
     * Generate number list control
     * @see  input
     */
    public function numberList($name, array $attrs = [], $override = false)
    {
        $default = [
            'start'=>1,
            'end'=>5,
        ];
        $attrs += $default;
        $start = $attrs['start'];
        $end = $attrs['end'];
        unset($attrs['start'],$attrs['end']);
        if (empty($attrs['options'])) {
            $options = [];
            for ($i=$start; $i <= $end; $i++) {
                $options[$i] = $i;
            }
            $attrs['options'] = $options;
        }
        $str = $this->dropdown($name, $attrs, $override);

        return $str;
    }

    /**
     * Generate date list control
     * @see  input
     */
    public function dateList($name, array $attrs = [], $override = false)
    {
        $default = [
            'startYear'=>2016,
            'endYear'=>2020,
            'months'=>array_flip(Helper::$months),
            'value'=>$this->value($name, date('Y-m-d')),
            'date'=>[
                'placeholder'=>'tgl --',
                'style'=>'display: inline; width: 70px; margin-right: 10px',
            ],
            'month'=>[
                'placeholder'=>'bln --',
                'style'=>'display: inline; width: 150px; margin-right: 10px',
            ],
            'year'=>[
                'placeholder'=>'thn --',
                'style'=>'display: inline; width: 100px; margin-right: 10px',
            ],
        ];
        $attrs += $default;
        $value = $attrs['value']?explode('-', $attrs['value']):date('Y-m-d');
        $startYear = $attrs['startYear'];
        $endYear = $attrs['endYear'];
        $optionsMonth = $attrs['months'];
        $date = $attrs['date'];
        $month = $attrs['month'];
        $year = $attrs['year'];
        unset($attrs['value'],$attrs['startYear'],$attrs['endYear'],$attrs['months'],
            $attrs['placeholder'],$attrs['date'],$attrs['month'],$attrs['year']);

        // date
        $a = $date+$attrs;
        $a['selected'] = $value[2];
        $a['start'] = 1;
        $a['end'] = 31;
        $str = $this->numberList($name.'[3]', $a, $override);

        // month
        $a = $month+$attrs;
        $a['selected'] = $value[1];
        $a['options'] = $optionsMonth;
        $str .= $this->monthList($name.'[2]', $a, $override);

        // date
        $a = $year+$attrs;
        $a['selected'] = $value[0];
        $a['start'] = $startYear;
        $a['end'] = $endYear;
        $str .= $this->numberList($name.'[1]', $a, $override);

        return $str;
    }

    /**
     * Method
     * @param string
     */
    public function setMethod($method)
    {
        $this->method = strtoupper($method);

        return $this;
    }

    /**
     * Label
     * @param array $labels
     */
    public function setLabels(array $labels)
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * LabelElement
     * @param array $element
     */
    public function setLabelElements($element)
    {
        $this->labelElement = $element;

        return $this;
    }

    /**
     * Form attrs
     * @param array $attrs
     */
    public function setAttrs(array $attrs)
    {
        $this->attrs = $attrs;

        return $this;
    }

    /**
     * Default label attrs
     * @param array $attrs
     */
    public function setDefaultLabelAttrs(array $attrs)
    {
        $this->labelAttrs = $attrs;

        return $this;
    }

    /**
     * Default control attrs
     * @param array $attrs
     */
    public function setDefaultControlAttrs(array $attrs)
    {
        $this->controlAttrs = $attrs;

        return $this;
    }

    /**
     * Get mapper value
     *
     * @param  string
     * @return string
     */
    protected function value($name, $default = null)
    {
        return ($this->mapper && $this->mapper->exists($name))?$this->mapper->get($name):$this->get($name, $default);
    }

    /**
     * Read field name
     *
     * @param  string
     * @return string
     */
    protected function readName($name)
    {
        return isset($this->labels[$name])?$this->labels[$name]:ucwords(str_replace('_', ' ', $name));
    }
}
