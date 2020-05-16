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
            <div>
                <span class="{$this->selector}" >
                    {$this->value}
                </span>
                 <i class="feather icon-check btn-outline-primary hidden" data-value="{$this->value}" data-name="{$this->column->getName()}" data-id="{$this->getKey()}" data-url="{$this->getUrl()}"></i>
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
.grid-editable,.icon-check{margin-left: 0.4rem}
CSS
        );
    }

    protected function addScript()
    {
        $script = <<<JS
            $(".{$this->selector}").on("click",function() {
                $(this).attr('contenteditable', true);
                $(this).next().removeClass("hidden");
            })
            $(".icon-check").on("click",function() {
                var obj = $(this);
                var url = obj.attr('data-url');
                var name = obj.attr('data-name');
                var old_value = obj.attr('data-value').trim();
                var rebr = new RegExp("<br>","g");
                var renbsp = new RegExp("&nbsp;","g");
                var value = obj.prev().html().replace(rebr,'').replace(renbsp,'').trim();
                if (value == old_value) {
                    obj.addClass("hidden").prev().attr('contenteditable', false);
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
                        if (data.status) {
                            obj.attr('data-value',value).addClass("hidden").prev().html(value).attr('contenteditable', false);
                            Dcat.success(data.message);
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
            })
JS;

        Admin::script($script);
    }


}
