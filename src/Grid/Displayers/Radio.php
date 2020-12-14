<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;

class Radio extends AbstractDisplayer
{
    public function display($options = [], $refresh = false)
    {
        if ($options instanceof \Closure) {
            $options = $options->call($this, $this->row);
        }

        return Admin::view('admin::grid.displayer.radio', [
            'options'  => $options,
            'key'      => $this->getKey(),
            'column'   => $this->column->getName(),
            'value'    => $this->value,
            'class'    => $this->getElementClass(),
            'resource' => $this->resource(),
            'refresh'  => $refresh,
        ]);
    }

    protected function getElementClass()
    {
        return 'grid-radio-'.$this->column->getName();
    }
}
