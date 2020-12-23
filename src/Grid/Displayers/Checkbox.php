<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;

class Checkbox extends AbstractDisplayer
{
    public function display($options = [], $refresh = false)
    {
        if ($options instanceof \Closure) {
            $options = $options->call($this, $this->row);
        }

        $this->value = Helper::array($this->value);

        return Admin::view('admin::grid.displayer.checkbox', [
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
        return 'grid-checkbox-'.$this->column->getName();
    }
}
