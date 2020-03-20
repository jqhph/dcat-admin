<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Support\Helper;

class Chip extends AbstractDisplayer
{
    public function display($style = 'primary', $max = null)
    {
        $class = $style;
        $background = '';
        $textColor = '';

        if (strpos($style, '#') === 0 || strpos($style, '(') !== false) {
            $class = '';
            $background = "style='background:{$style}'";
            $textColor = 'text-white';
        }

        return collect($this->value($max))->map(function ($name) use ($class, $background, $textColor) {
            return <<<HTML
<div class="chip chip-{$class}" {$background}>
  <div class="chip-body">
    <div class="chip-text {$textColor}">{$name}</div>
  </div>
</div>
HTML;
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
