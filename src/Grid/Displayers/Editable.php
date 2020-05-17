<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;

class Editable extends AbstractDisplayer
{
    protected $selector = 'grid-editable';

    public function display($refresh = false)
    {
        $this->addScript();
        $this->addStyle();

        $label = __('admin.save');

        return <<<HTML
<div>
    <span class="{$this->selector}" contenteditable="true">
        {$this->value}
    </span>
    <span class="save btn-outline-primary hidden" 
        data-value="{$this->value}" 
        data-name="{$this->column->getName()}" 
        data-id="{$this->getKey()}" 
        data-refresh="{$refresh}"
        data-url="{$this->getUrl()}">
        {$label}
    </span>
</div>
HTML;
    }

    protected function getUrl()
    {
        return $this->resource().'/'.$this->getKey();
    }

    protected function addStyle()
    {
        $color = Admin::color()->link();

        Admin::style(
            <<<CSS
.grid-editable{border-bottom:dashed 1px $color;color: $color;display: inline-block}
.grid-editable+.save{margin-left: 0.55rem;}
CSS
        );
    }

    protected function addScript()
    {
        $script = <<<JS
$(".{$this->selector}").on("click", function() {
    $(this).next().removeClass("hidden");
}).on('blur', function () {
    var icon = $(this).next();
    setTimeout(function () {
        icon.addClass("hidden")
    }, 200)
});
$('.{$this->selector}+.save').on("click",function() {
    var obj = $(this),
        url = obj.data('url'),
        name = obj.data('name'),
        refresh = obj.data('refresh'),
        old_value = obj.data('value').trim(),
        value = obj.prev().html().replace(new RegExp("<br>","g"), '').replace(new RegExp("&nbsp;","g"), '').trim();
    
    var data = {
        _token: Dcat.token,
        _method: 'PUT'
    };
    data[name] = value;
    Dcat.NP.start();
    $.ajax({
        url: url,
        type: "POST",
        data: data,
        success: function (data) {
            if (data.status) {
                obj.attr('data-value',value).addClass("hidden").prev().html(value);
                Dcat.success(data.message);
                
                refresh && Dcat.reload()
            } else {
                obj.prev().html(old_value);
                Dcat.error(data.message);
            }
        },
        error:function(a,b,c) {
            obj.prev().html(old_value);
            Dcat.handleAjaxError(a, b, c);
        },
        complete:function(a,b) {
            Dcat.NP.done();
        }
    });
    
    return false;
})
JS;

        Admin::script($script);
    }
}
