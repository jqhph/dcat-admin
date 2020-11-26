<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;

class Radio extends AbstractDisplayer
{
    public function display($options = [], $refresh = false)
    {
        if ($options instanceof \Closure) {
            $options = $options->call($this, $this->row);
        }

        $radios = '';
        $name = $this->column->getName();

        foreach ($options as $value => $label) {
            $checked = ($value == $this->value) ? 'checked' : '';

            $radios .= <<<EOT
<div class="vs-radio-con">
    <input type="radio" name="grid-radio-{$name}[]" value="{$value}" $checked >
    <span class="vs-radio">
      <span class="vs-radio--border"></span>
      <span class="vs-radio--circle"></span>
    </span>
    <span class="">{$label}</span>
</div>           
EOT;
        }

        Admin::script($this->addScript($refresh));

        return <<<EOT
<form class="form-group {$this->getElementClass()}" style="text-align: left" data-key="{$this->getKey()}">
    $radios
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
        return 'grid-radio-'.$this->column->getName();
    }

    protected function addScript($refresh)
    {
        return <<<JS
(function () {
    $('form.{$this->getElementClass()}').on('submit', function () {
        var value = $(this).find('input:radio:checked').val(), 
            btn = $(this).find('[type="submit"]'),
            reload = '{$refresh}';
        
        if (btn.attr('loading')) {
            return;
        }
        btn.attr('loading', 1);
        btn.buttonLoading();
    
        $.ajax({
            url: "{$this->resource()}/" + $(this).data('key'),
            type: "POST",
            data: {
                {$this->column->getName()}: value,
                _method: 'PUT'
            },
            success: function (data) {
                btn.buttonLoading(false);
                btn.removeAttr('loading');
                Dcat.success(data.message);
                reload && Dcat.reload()
            },
            error: function (a, b, c) {
                btn.buttonLoading(false);
                btn.removeAttr('loading');
                Dcat.handleAjaxError(a, b, c);
            },
        });
    
        return false;
    });
})()
JS;
    }
}
