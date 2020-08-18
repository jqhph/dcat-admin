<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;

class Checkbox extends AbstractDisplayer
{
    public function display($options = [], $refresh = false)
    {
        if ($options instanceof \Closure) {
            $options = $options->call($this, $this->row);
        }

        $checkboxes = '';
        $name = $this->column->getName();

        if (is_string($this->value)) {
            $this->value = explode(',', $this->value);
        }

        $this->value = Helper::array($this->value);

        foreach ($options as $value => $label) {
            $checked = in_array($value, $this->value) ? 'checked' : '';

            $checkboxes .= <<<EOT
<div class="vs-checkbox-con vs-checkbox-primary" style="margin-bottom: 4px">
    <input type="checkbox" name="grid-checkbox-{$name}[]" value="{$value}" $checked >
    <span class="vs-checkbox vs-checkbox-sm"><span class="vs-checkbox--check"><i class="vs-icon feather icon-check"></i></span></span>
    <span class="">{$label}</span>
</div>
EOT;
        }

        Admin::script($this->addScript($refresh));

        return <<<EOT
<form class="form-group {$this->getElementClass()}" style="text-align:left;" data-key="{$this->getKey()}">
    $checkboxes
    <button type="submit" class="btn btn-primary btn-sm pull-left">
        <i class="feather icon-save"></i>&nbsp;{$this->trans('save')}
    </button>
    <button type="reset" class="btn btn-white btn-sm pull-left" style="margin-left:5px;">
        <i class="feather icon-trash"></i>&nbsp;{$this->trans('reset')}
    </button>
</form>
EOT;
    }

    protected function getElementClass()
    {
        return 'grid-checkbox-'.$this->column->getName();
    }

    protected function addScript($refresh)
    {
        return <<<JS
(function () {
    $('form.{$this->getElementClass()}').off('submit').on('submit', function () {
        var values = $(this).find('input:checkbox:checked').map(function (_, el) {
            return $(el).val();
        }).get(), 
        btn = $(this).find('[type="submit"]'),
        reload = '{$refresh}';
        
        if (btn.attr('loading')) return;
        btn.attr('loading', 1);
        btn.buttonLoading();
    
        var data = {
            {$this->column->getName()}: values,
            _token: Dcat.token,
            _method: 'PUT'
        };
        
        $.ajax({
            url: "{$this->resource()}/" + $(this).data('key'),
            type: "POST",
            contentType: 'application/json;charset=utf-8',
            data: JSON.stringify(data),
            success: function (data) {
                btn.buttonLoading(false);
                btn.removeAttr('loading');
                Dcat.success(data.message);
                reload && Dcat.reload();
            },
            error: function (a, b, c) {
                btn.buttonLoading(false);
                btn.removeAttr('loading');
                Dcat.handleAjaxError(a, b, c);
            },
        });
    
        return false;
    });
})();
JS;
    }
}
