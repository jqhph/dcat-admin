<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Support\Helper;

class Label extends AbstractDisplayer
{
    protected $baseClass = 'label';
    protected $stylePrefix = 'bg';

    public function display($style = 'primary', $max = null)
    {
        $class = $style;
        $background = '';

        if (strpos($style, '#') === 0 || strpos($style, '(') !== false) {
            $class = '';
            $background = "style='background:{$style}'";
        }

        return collect($this->value($max))->map(function ($name) use ($class, $background) {
            return "<span class='{$this->baseClass} {$this->stylePrefix}-{$class}' {$background}>$name</span>";
        })->implode('&nbsp;');
    }

    protected function value($max)
    {
        $values = Helper::array($this->value);

        if ($max && count($values) > $max) {
            $values = array_slice($values, 0, $max);
            $values[] = '...';
        }

        return $values;
    }
}
