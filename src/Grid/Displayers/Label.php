<?php

namespace Dcat\Admin\Grid\Displayers;

use Illuminate\Contracts\Support\Arrayable;

class Label extends AbstractDisplayer
{
    public function display($style = 'success', $max = null)
    {
        if ($this->value instanceof Arrayable) {
            $this->value = $this->value->toArray();
        }

        $values = (array) $this->value;
        if ($max && count($values) > $max) {
            $values = array_slice($values, 0, $max);
            $values[] = '...';
        }

        return collect($values)->map(function ($name) use ($style) {
            return "<span class='label label-{$style}'>$name</span>";
        })->implode('&nbsp;');
    }
}
