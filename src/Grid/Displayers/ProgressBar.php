<?php

namespace Dcat\Admin\Grid\Displayers;

class ProgressBar extends AbstractDisplayer
{
    public function display($style = 'primary', $size = 'sm', $max = 100)
    {
        $style = collect((array) $style)->map(function ($style) {
            return 'progress-bar-'.$style;
        })->implode(' ');

        return <<<EOT

<div class="progress progress-$size">
    <div class="progress-bar $style" data-width="{$this->value}%" aria-valuemin="0" aria-valuemax="$max" >
      <span class="sr-only">{$this->value}</span>
    </div>
</div>

EOT;
    }
}
