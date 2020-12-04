<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form\Field;

class Divide extends Field
{
    public function render()
    {
        if ($this->label) {
            return <<<HTML
<div class="row">
  <div class="col"><hr/></div>
  <div class="col-auto text-muted h4">{$this->label}</div>
  <div class="col"><hr/></div>
</div>
HTML;
        }
        return '<hr/>';
    }
}
