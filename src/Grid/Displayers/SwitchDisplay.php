<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;

class SwitchDisplay extends AbstractDisplayer
{
    protected $color;

    public function color($color)
    {
        $this->color = Admin::color()->get($color);
    }

    public function display(string $color = '', $refresh = false)
    {
        if ($color instanceof \Closure) {
            $color->call($this->row, $this);
        } else {
            $this->color($color);
        }

        $column = $this->column->getName();
        $checked = $this->value ? 'checked' : '';
        $color = $this->color ?: Admin::color()->primary();
        $url = $this->url();

        return Admin::view(
            'admin::grid.displayer.switch',
            compact('column', 'color', 'refresh', 'checked', 'url')
        );
    }

    protected function url()
    {
        return $this->resource().'/'.$this->getKey();
    }
}
