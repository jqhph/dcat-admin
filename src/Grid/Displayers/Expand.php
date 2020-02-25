<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;

class Expand extends AbstractDisplayer
{
    protected $button;

    /**
     * @var array
     */
    protected static $counter = 0;

    public function button($button)
    {
        $this->button = $button;
    }

    public function display($callbackOrButton = null)
    {
        $html = $this->value;
        if ($callbackOrButton && $callbackOrButton instanceof \Closure) {
            $callback = $callbackOrButton->bindTo($this->row);

            $html = $callback($this);
            if ($html instanceof Renderable) {
                $html = $html->render();
            }
        } elseif ($callbackOrButton && is_string($callbackOrButton)) {
            $this->button = $callbackOrButton;
        }

        $this->setupScript();

        $key = $this->getDataKey();

        $button = is_null($this->button) ? $this->value : $this->button;

        return <<<EOT
<span class="grid-expand" data-inserted="0" data-key="{$key}" data-toggle="collapse" data-target="#grid-collapse-{$key}">
   <a href="javascript:void(0)"><i class="fa fa-angle-double-right"></i>  $button</a>
</span>
<template class="grid-expand-{$key}">
    <div id="grid-collapse-{$key}" class="collapse">$html</div>
</template>
EOT;
    }

    /**
     * @return string
     */
    protected function getDataKey()
    {
        $key = $this->key() ?: Str::random(8);

        static::$counter++;

        return $this->grid->getName().$key.'-'.static::$counter;
    }

    protected function setupScript()
    {
        $script = <<<'JS'
$('.grid-expand').off('click').click(function () {
    
    if ($(this).data('inserted') == '0') {
    
        var key = $(this).data('key');
        var row = $(this).closest('tr');
        var html = $('template.grid-expand-'+key).html();

        row.after("<tr><td colspan='"+(row.find('td').length)+"' style='padding:0 !important; border:0;height:0;'>"+html+"</td></tr>");

        $(this).data('inserted', 1);
    }
    
    $("i", this).toggleClass("fa-angle-double-right fa-angle-double-down");
});
JS;
        Admin::script($script);
    }
}
