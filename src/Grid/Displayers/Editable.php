<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;

class Editable extends AbstractDisplayer
{
    protected $selector = 'grid-column-editable';

    public function display($refresh = false)
    {
        $this->addScript();
        $this->addStyle();

        $value = Helper::render($this->value);

        $label = __('admin.save');

        return <<<HTML
<div class="d-inline">
    <span class="{$this->selector}" contenteditable="true">{$value}</span>
    <span class="save hidden" 
        data-value="{$this->value}" 
        data-name="{$this->column->getName()}" 
        data-id="{$this->getKey()}" 
        data-refresh="{$refresh}"
        data-url="{$this->getUrl()}">
        {$label}
    </span>
    <div class="d-none"></div>
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
        $primary = Admin::color()->primary();

        Admin::style(
            <<<CSS
.{$this->selector}{border-bottom:dashed 1px $color;color: $color;display: inline-block; -webkit-user-modify: read-write-plaintext-only;}
.{$this->selector}+.save{margin-left: 0.4rem;color: $color}
body.dark-mode .{$this->selector}{color: $primary;border-color: $primary;}
body.dark-mode .{$this->selector}+.save{color: $primary}
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
        old_value = obj.data('value'),
        value = obj.prev().html(),
        tmp = obj.next();
    
    tmp.html(value);

    value = tmp.text().replace(new RegExp("<br>","g"), '').replace(new RegExp("&nbsp;","g"), '').trim();
    
    var data = {
        _token: Dcat.token,
        _method: 'PUT'
    };
    if (name.indexOf('.') === -1) {
        data[name] = value;
    } else {
        name = name.split('.');
        
        data[name[0]] = {};
        data[name[0]][name[1]] = value;
    }
    
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
