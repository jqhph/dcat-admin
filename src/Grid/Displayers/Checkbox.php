<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Support\Helper;
use Illuminate\Support\Arr;

class Checkbox extends Editable
{
    protected $type = 'checkbox';

    protected $view = 'admin::grid.displayer.editinline.checkbox';

    public function display($options = [], $refresh = false)
    {
        $options['options'] = $options;
        $options['refresh'] = $refresh;
        $options['checkbox'] = $this->renderCheckbox($options['options']);

        return parent::display($options);
    }

    protected function renderCheckbox($options)
    {
        $checkbox = \Dcat\Admin\Widgets\Checkbox::make($this->getName().'[]');
        $checkbox->options($options);
        $checkbox->class('ie-input');

        return $checkbox;
    }

    protected function getValue()
    {
        return implode('; ', Arr::only($this->options['options'], Helper::array($this->value, false)));
    }

    protected function getOriginal()
    {
        return json_encode(array_map(function ($value) {
            return (string) $value;
        }, Helper::array($this->column->getOriginal(), false)));
    }
}
