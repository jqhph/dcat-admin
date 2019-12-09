<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;
use Illuminate\Support\Arr;

class SwitchGroup extends SwitchDisplay
{
    public function display($columns = [], string $color = '')
    {
        if ($columns instanceof \Closure) {
            $columns = $columns->call($this->row, $this);
        }

        if ($color) {
            if (method_exists($this, $color)) {
                $this->$color();
            } else {
                $this->color($color);
            }
        }

        if (! Arr::isAssoc($columns)) {
            $labels = array_map('admin_trans_field', $columns);
            $columns = array_combine($columns, $labels);
        }

        $html = [];

        foreach ($columns as $column => $label) {
            $html[] = $this->buildSwitch($column, $label);
        }

        return '<table>'.implode('', $html).'</table>';
    }

    protected function buildSwitch($name, $label = '')
    {
        $class = 'grid-switch-group-'.$this->grid->getName();
        $keys = collect(explode('.', $name));

        if ($keys->isEmpty()) {
            $elementName = $name;
        } else {
            $elementName = $keys->shift().$keys->reduce(function ($carry, $val) {
                return "{$carry}[{$val}]";
            });
        }

        $script = <<<JS
(function () {
    var swt = $('.$class'), t;
    function init(){
        swt.each(function(){
             t = $(this);
             new Switchery(t[0], t.data())
        })
    } 
    init();
    swt.change(function(e) {
        var t = $(this), id=t.data('key'),checked = t.is(':checked'), name = t.attr('name'), data = {
            _token: LA.token,
            _method: 'PUT'
        };
        data[name] = checked ? 1 : 0;
        LA.NP.start();
    
         $.ajax({
            url: "{$this->resource()}/" + id,
            type: "POST",
            data: data,
            success: function (d) {
                LA.NP.done();
                 if (d.status) {
                    LA.success(d.message);
                } else {
                    LA.error(d.message);
                }
            }
        });
    });
})();
JS;
        Admin::script($script);

        $key = $this->row->{$this->grid->keyName()};
        $checked = $this->row->$name ? 'checked' : '';

        return <<<EOT
<tr style="height:28px;color:#555">
    <td><strong><small>$label:</small></strong>&nbsp;&nbsp;&nbsp;</td>
    <td><input name="{$elementName}" data-key="$key" $checked type="checkbox" class="$class" data-size="small" data-color="{$this->color}"/></td>
</tr>
EOT;
    }
}
