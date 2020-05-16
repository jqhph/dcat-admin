<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;

class Editable extends AbstractDisplayer
{
    protected $selector = 'grid-editable';

    public function display()
    {
        $this->addScript();
        $this->addStyle();

        return <<<HTML
<div class="{$this->selector}">
    <span data-value="{$this->value}" data-name="{$this->column->getName()}" data-id="{$this->getKey()}" data-url="{$this->getUrl()}" >
        {$this->value}
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
CSS
        );
    }

    protected function addScript()
    {
        $script = <<<JS
            $(".{$this->selector} span").on("click",function() {
                $(this).attr('contenteditable', true);
            }).on("blur",function() {
                var obj = $(this);
                var url = obj.attr('data-url').trim();
                var name = obj.attr('data-name').trim();
                var old_value = obj.attr('data-value').trim();
                var value = obj.html().trim();
                if (value == old_value) {
                    return;
                }
                
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
                        Dcat.NP.done();
                        if (data.status) {
                            obj.attr('data-value',value);
                            Dcat.success(data.message);
                        } else {
                            obj.html(old_value);
                            Dcat.error(data.message);
                        }
                    },
                    error:function(a,b,c) {
                      Dcat.NP.done();
                      obj.html(old_value);
                      Dcat.handleAjaxError(a, b, c);
                    }
                });
            })
JS;

        Admin::script($script);
    }
}
