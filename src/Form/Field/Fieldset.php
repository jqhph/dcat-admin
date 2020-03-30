<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;

class Fieldset
{
    protected $name = '';

    public function __construct()
    {
        $this->name = uniqid('fieldset-');
    }

    public function start($title)
    {
        $script = <<<JS
$('.{$this->name}-title').on('click', function () {
    $("i", this).toggleClass("fa-angle-double-down fa-angle-double-up");
});
JS;

        Admin::script($script);

        return <<<HTML
<div>
    <div class="mt-2 text-center" style="height: 20px; border-bottom: 1px solid #eee; margin-bottom: 25px">
      <span style="font-size: 16px; background-color: #ffffff; padding: 0 10px;">
        <a data-toggle="collapse" href="#{$this->name}" class="{$this->name}-title">
          <i class="fa fa-angle-double-up"></i>&nbsp;&nbsp;{$title}
        </a>
      </span>
    </div>
    <div class="collapse in" id="{$this->name}">
HTML;
    }

    public function end()
    {
        return '</div></div>';
    }

    public function collapsed()
    {
        $script = <<<JS
$("#{$this->name}").removeClass("in");
$(".{$this->name}-title i").toggleClass("fa-angle-double-down fa-angle-double-up");
JS;

        Admin::script($script);

        return $this;
    }
}
