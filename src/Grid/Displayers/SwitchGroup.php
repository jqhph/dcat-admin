<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;
use Illuminate\Support\Arr;

class SwitchGroup extends SwitchDisplay
{
    protected $selector = 'grid-column-switch-group';

    public function display($columns = [], string $color = '')
    {
        if ($columns instanceof \Closure) {
            $columns = $columns->call($this->row, $this);
        }

        $this->addScript();

        if ($color) {
            $this->color($color);
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
        $checked = Arr::get($this->row->toArray(), $name) ? 'checked' : '';
        $color = $this->color ?: Admin::color()->primary();

        return <<<EOT
<tr style="box-shadow: none;background: transparent">
    <td style="padding: 3px 0;height:23px;">{$label}:&nbsp;&nbsp;&nbsp;</td>
    <td style="padding: 3px 0;height:23px;"><input name="{$name}" data-path="{$this->resource()}" data-key="{$this->getKey()}" $checked 
        type="checkbox" class="{$this->selector}" data-size="small" data-color="{$color}"/></td>
</tr>
EOT;
    }

    protected function addScript()
    {
        $script = <<<JS
(function () {
    var swt = $('.{$this->selector}'), that;
    function init(){
        swt.each(function(){
             that = $(this);
             that.parent().find('.switchery').remove();
             
             new Switchery(that[0], that.data())
        })
    } 
    init();
    swt.off('change').change(function(e) {
        var that = $(this), 
            id = that.data('key'),
            url = that.data('path') + '/' + id,
            checked = that.is(':checked'), 
            name = that.attr('name'), 
            data = {
                _token: Dcat.token,
                _method: 'PUT'
            },
            value = checked ? 1 : 0;
        
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
            success: function (d) {
                Dcat.NP.done();
                 if (d.status) {
                    Dcat.success(d.message);
                } else {
                    Dcat.error(d.message);
                }
            }
        });
    });
})();
JS;
        Admin::script($script);
    }
}
