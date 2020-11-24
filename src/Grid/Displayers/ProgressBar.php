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
<div class="shadow-100 progress $style">
  <div class="progress-bar" role="progressbar" aria-valuenow="{$this->value}" aria-valuemin="0" aria-valuemax="{$max}" style="width:{$this->value}%"></div>
</div>
EOT;
    }
}
