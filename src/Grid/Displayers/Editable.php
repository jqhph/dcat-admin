<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;

class Editable extends AbstractDisplayer
{

    protected $class = "grid-editable";

    public function display()
    {
        $id = $this->getKey();
        $name = $this->column->getName();
        $resource = $this->resource() . "/" . $id;
        $this->setupScript();
        return "<span class=\"{$this->class}\" style=\"border-bottom:dashed 1px #0088cc\" data-value=\"{$this->value}\"  data-name=\"{$name}\" data-id=\"{$id}\" data-url=\"{$resource}\" >{$this->value}</span>";
    }

    protected function setupScript()
    {
        $script = <<<JS
            $(".{$this->class}").on("click",function() {
                $(this).attr('contenteditable', true);
            })
            $(".{$this->class}").on("blur",function() {
                var obj = $(this);
                var url = obj.attr('data-url').trim();
                var name = obj.attr('data-name').trim();
                var old_value = obj.attr('data-value').trim();
                var value = obj.html().trim();
                if (value != old_value) {
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
                                obj.attr('data-value',value)
                                Dcat.success(data.message);
                            } else {
                                obj.html(old_value)
                                Dcat.error(data.message);
                            }
                        },
                        error:function(a,b,c) {
                          Dcat.NP.done();
                          obj.html(old_value)
                          Dcat.handleAjaxError(a, b, c);
                        }
                    });
                }
            })
            
JS;
        Admin::script($script);
    }

}
