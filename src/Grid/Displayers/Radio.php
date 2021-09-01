<?php

namespace Dcat\Admin\Grid\Displayers;

use Illuminate\Support\Arr;

class Radio extends Editable
{
    protected $type = 'radio';

    protected $view = 'admin::grid.displayer.editinline.radio';

    public function display($options = [], $refresh = false)
    {
        $options['options'] = $options;
        $options['refresh'] = $refresh;
        $options['radio'] = $this->renderRadio($options['options']);

        return parent::display($options);
    }

    protected function renderRadio($options)
    {
        $checkbox = \Dcat\Admin\Widgets\Radio::make($this->getName());
        $checkbox->options($options);
        $checkbox->class('ie-input');

        return $checkbox;
    }

    protected function getValue()
    {
        return Arr::get($this->options['options'], $this->value);
    }
}
