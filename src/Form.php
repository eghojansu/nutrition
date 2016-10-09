<?php

namespace Nutrition;

use Base;
use DB\Cursor;

class Form
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
        $this->validation = new Validation($mapper);
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

    protected function findFilter($field, $filter, array $find = [], array $replace = [])
    {
        $filters = $this->validation->getFilter($field);
        $rules = [];
        if (isset($filters[$filter])) {
            foreach ($find as $key=>$name) {
                $name = isset($replace[$key])?$replace[$key]:$name;
                $value = isset($filters[$filter][$key])?$filters[$filter][$key]:null;
                if (is_null($value)) {
                    continue;
                }
                $rules[$name] = $value;
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
     * Open form
     * @return string
     */
    public function open()
    {
        $attrs = $this->attrs;
        $attrs['method'] = $this->method;
        $str = '<form'.$this->renderAttribute($attrs).'>'.$this->onOpen();

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
        $str = '<'.$element.$this->renderAttribute($attrs).'>'.$attrs['value'].'</'.$element.'>';

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
        $attrs = ($override?$attrs:$this->mergeAttribute($this->labelAttrs, $attrs))+$default;
        $str = '<'.$this->labelElement.$this->renderAttribute($attrs).'>'.$this->readName($name).'</'.$this->labelElement.'>';

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
        ]+$this->findFilter($name,'required',['required']);;
        $attrs = ($override?$attrs:$this->mergeAttribute($this->controlAttrs, $attrs))+$default;
        $str = '<input'.$this->renderAttribute($attrs).'>';

        return $str;
    }

    /**
     * Generate text control
     * @see  input
     */
    public function text($name, array $attrs = [], $override = false)
    {
        $default = []+$this->findFilter($name,'string',['min','max'],['minlength','maxlength']);
        $str = $this->input('text', $name, $attrs+$default, $override);

        return $str;
    }

    /**
     * Generate password control
     * @see  input
     */
    public function password($name, array $attrs = [], $override = false)
    {
        $default = []+$this->findFilter($name,'string',['min','max'],['minlength','maxlength']);
        $str = $this->input('password', $name, $attrs+$default, $override);

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
        $default = [
            'value'=>null,
        ];
        $str = $this->input('hidden', $name, $attrs+$default, $override);

        return $str;
    }

    /**
     * Generate radio control
     * @see  input
     */
    public function radio($name, array $attrs = [], $override = false)
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
            $str = '<label'.$this->renderAttribute(is_array($wrapLabel)?$wrapLabel:[]).'>'.$str.'</label>';
        }

        return $str;
    }

    /**
     * Generate checkbox control
     * @see  input
     */
    public function checkbox($name, array $attrs = [], $override = false)
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
        $str = $this->input('checkbox', $name, $attrs, $override).' '.$label;
        if ($wrapLabel) {
            $str = '<label'.$this->renderAttribute(is_array($wrapLabel)?$wrapLabel:[]).'>'.$str.'</label>';
        }

        return $str;
    }

    /**
     * Generate radio list control
     * @see  input
     */
    public function radioList($name, array $attrs = [], $override = false)
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
    public function checkboxList($name, array $attrs = [], $override = false)
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
                $str .= $this->checkbox($name, $attrs, $override);
            }
        }

        return $str;
    }

    /**
     * Generate combobox control
     * @see  input
     */
    public function select($name, array $attrs = [], $override = false)
    {
        $default = [
            'name'=>$name,
            'options'=>[],
            'selected'=>$this->value($name),
            'renderer'=>null,
            'placeholder'=>'-- pilih '.$this->readName($name),
        ];
        $attrs = ($override?$attrs:$this->mergeAttribute($this->controlAttrs, $attrs))+$default;
        $options = $attrs['options'];
        $selected = $attrs['selected'];
        $renderer = $attrs['renderer'];
        unset($attrs['options'],$attrs['selected'],$attrs['renderer']);

        if ($renderer && is_callable($renderer)) {
            $optionStr = call_user_func_array($renderer, [$selected,$options]);
        } else {
            $optionStr = '';
            if ($attrs['placeholder']) {
                $optionStr .= '<option value="">'.$attrs['placeholder'].'</option>';
            }
            foreach ($options as $label => $value) {
                $a = ['value'=>$value];
                if ($value == $selected) {
                    $a[] = 'selected';
                }
                $optionStr .= '<option'.$this->renderAttribute($a).'>'.$label.'</option>';
            }
        }
        $str = '<select'.$this->renderAttribute($attrs).'>'.$optionStr.'</select>';

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
        ];
        $attrs = ($override?$attrs:$this->mergeAttribute($this->controlAttrs, $attrs))+$default;
        $value = $attrs['value'];
        unset($attrs['value']);
        $str = '<textarea'.$this->renderAttribute($attrs).'>'.$value.'</textarea>';

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
        $str = $this->select($name, $attrs, $override);

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
        $str = $this->select($name, $attrs, $override);

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
        return $this->mapper?$this->mapper->get($name):$default;
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

    /**
     * Render attributes
     *
     * @param  array
     * @return string
     */
    protected function renderAttribute(array $attrs)
    {
        $str = '';
        foreach ($attrs as $key => $value) {
            $str .= ' '.(is_numeric($key)?$value.'=""':$key.'="'.$value.'"');
        }

        return $str;
    }

    /**
     * Merge b to a
     *
     * @param  array
     * @param  array
     * @return array
     */
    protected function mergeAttribute(array $a, array $b)
    {
        foreach ($b as $key => $value) {
            if (isset($a[$key])) {
                $a[$key] .= ' '.$value;
            } else {
                $a[$key] = $value;
            }
        }

        return $a;
    }
}
