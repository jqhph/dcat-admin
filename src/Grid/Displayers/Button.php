<?php

namespace Dcat\Admin\Grid\Displayers;

class Button extends AbstractDisplayer
{
    public function display($style = 'primary')
    {
        $style = collect((array) $style)->map(function ($style) {
            return 'btn-'.$style;
        })->implode(' ');

        return "<span class='btn btn-sm $style'>{$this->value}</span>";
    }
}
