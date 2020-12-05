<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form\Field;

class Divide extends Field
{
    public function __construct($label = null)
    {
        $this->label = $label;
    }

    public function render()
    {
        if (! $this->label) {
            return '<hr/>';
        }

        return <<<HTML
<div class="mt-2 text-center" style="height: 20px; border-bottom: 1px solid #eee; margin-bottom: 25px">
  <span style="font-size: 16px; background-color: #ffffff; padding: 0 10px;">
    {$this->label}
  </span>
</div>
HTML;
    }
}
