<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\RemoteRenderable;
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

    protected function generateElementId()
    {
        $key = Str::random(8);

        return 'grid-modal-'.$this->grid->getName().$key;
    }

    protected function addRenderableScript(string $modalId, string $url)
    {
        $script = <<<JS
(function () {
    var modal = $('#{$modalId}');
    
    modal.on('show.bs.modal', function (e) {
        modal.find('.modal-body').html('<div style="min-height:150px"></div>');
    
        modal.find('.modal-body').loading();
        
        $.ajax('{$url}').then(function (data) {
            modal.find('.modal-body').html(data);
        });
    })
})();
JS;

        Admin::script($script);
    }

    protected function setUpRemoteRenderable(RemoteRenderable $renderable)
    {
        $renderable::collectAssets();
    }

    public function display($callbackOrButton = null)
    {
        $html = $this->value;
        $remoteUrl = '';

        if ($callbackOrButton && $callbackOrButton instanceof \Closure) {
            $callback = $callbackOrButton->bindTo($this->row);

            $html = $callback($this);
            if ($html instanceof Renderable) {
                $html = $html->render();
            }
        } elseif ($callbackOrButton && is_string($callbackOrButton)) {
            $this->button = $callbackOrButton;
        } elseif ($callbackOrButton instanceof RemoteRenderable) {
            $html = '<div style="min-height: 150px"></div>';

            $this->setUpRemoteRenderable($callbackOrButton);

            $remoteUrl = $callbackOrButton->getUrl();
        } elseif (is_string($callbackOrButton) && is_subclass_of($callbackOrButton, RemoteRenderable::class)) {
            $html = '<div style="min-height: 150px"></div>';

            $this->setUpRemoteRenderable($renderable = $callbackOrButton::make());

            $remoteUrl = $renderable->getUrl();
        }

        $this->addScript($remoteUrl);

        $key = $this->getDataKey();

        $button = is_null($this->button) ? $this->value : $this->button;

        return <<<EOT
<span class="grid-expand" data-inserted="0" data-id="{$this->getKey()}" data-key="{$key}" data-toggle="collapse" data-target="#grid-collapse-{$key}">
   <a href="javascript:void(0)"><i class="feather icon-chevrons-right"></i>  $button</a>
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
        $key = $this->getKey() ?: Str::random(8);

        static::$counter++;

        return $this->grid->getName().$key.'-'.static::$counter;
    }

    protected function addScript(?string $remoteUrl)
    {
        $script = <<<JS
$('.grid-expand').off('click').on('click', function () {
    var _th = $(this), url = "{$remoteUrl}";
    
    if ($(this).data('inserted') == '0') {
    
        var key = _th.data('key');
        var row = _th.closest('tr');
        var html = $('template.grid-expand-'+key).html();
        var id = 'expand-'+key+Dcat.helpers.random(10);
        var rowKey = _th.data('id');
        
        $(this).attr('data-expand', '#'+id);

        row.after("<tr id="+id+"><td colspan='"+(row.find('td').length)+"' style='padding:0 !important; border:0;height:0;'>"+html+"</td></tr>");
        
        if (url) {
            var collapse = $('#grid-collapse-'+key);
            collapse.find('div').loading();
            $('.dcat-loading').css({position: 'inherit', 'padding-top': '70px'});
        
            $.ajax(url+'&key='+rowKey).then(function (data) {
                collapse.html(data);
            });
        }

        $(this).data('inserted', 1);
    } else {
        if ($("i", this).hasClass('icon-chevrons-right')) {
            $(_th.data('expand')).show();
        } else {
            setTimeout(function() {
              $(_th.data('expand')).hide();
            }, 250);
        }
    }
    
    $("i", this).toggleClass("icon-chevrons-right icon-chevrons-down");
});
JS;
        Admin::script($script);
    }
}
