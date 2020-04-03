<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;

class Chip extends AbstractDisplayer
{
    public function display($style = 'primary', $max = null)
    {
        if (! $value = $this->value($max)) {
            return;
        }

        $original = $this->column->getOriginal();
        $defaultStyle = is_array($style) ? ($style['default'] ?? 'primary') : 'primary';

        [$background, $textColor] = $this->formatStyle(
            is_array($style) ?
                (is_scalar($original) ? ($style[$original] ?? $defaultStyle) : current($style))
                : $style
        );

        return collect($value)->map(function ($name) use ($background, $textColor) {
            return <<<HTML
<div class="chip" {$background}>
  <div class="chip-body">
    <div class="chip-text {$textColor}">{$name}</div>
  </div>
</div>
HTML;
        })->implode('&nbsp;');
    }

    protected function formatStyle($style)
    {
        $background = '';
        $textColor = '';

        if ($style !== 'default') {
            $style = Admin::color()->get($style);
            $background = "style='background:{$style}'";
            $textColor = 'text-white';
        }

        return [$background, $textColor];
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
