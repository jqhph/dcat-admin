<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;
use Illuminate\Contracts\Support\Renderable;

class Expand extends AbstractDisplayer
{
    protected $button;

    /**
     * @var array
     */
    protected static $counter = [];

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
        }  elseif ($callbackOrButton && is_string($callbackOrButton)) {
            $this->button = $callbackOrButton;
        }

        $this->setupScript();

        $key = $this->getDataKey();

        $button = $this->button ?? $this->value;

        return <<<EOT
<span class="grid-expand" data-inserted="0" data-key="{$key}" data-toggle="collapse" data-target="#grid-collapse-{$key}">
   <a class="btn btn-xs btn-default"><i class="fa fa-caret-right"></i>  $button</a>
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
        $key = $this->getKey();

        static::$counter[$key] = static::$counter[$key] ?? 0;
        static::$counter[$key]++;

        return $key.static::$counter[$key];
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
    
    $("i", this).toggleClass("fa-caret-down fa-caret-right");
});
JS;
        Admin::script($script);
    }
}
