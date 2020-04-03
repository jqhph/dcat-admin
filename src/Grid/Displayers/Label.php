<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;

class Label extends AbstractDisplayer
{
    protected $baseClass = 'label';
    protected $stylePrefix = 'bg';

    public function display($style = 'primary', $max = null)
    {
        if (! $value = $this->value($max)) {
            return;
        }

        $original = $this->column->getOriginal();
        $defaultStyle = is_array($style) ? ($style['default'] ?? 'default') : 'default';

        [$class, $background] = $this->formatStyle(
            is_array($style) ?
                (is_scalar($original) ? ($style[$original] ?? $defaultStyle) : current($style))
                : $style
        );

        return collect($value)->map(function ($name) use ($class, $background) {
            return "<span class='{$this->baseClass} {$this->stylePrefix}-{$class}' {$background}>$name</span>";
        })->implode('&nbsp;');
    }

    protected function formatStyle($style)
    {
        $class = 'default';
        $background = '';

        if ($style !== 'default') {
            $class = '';

            $style = Admin::color()->get($style);
            $background = "style='background:{$style}'";
        }

        return [$class, $background];
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
