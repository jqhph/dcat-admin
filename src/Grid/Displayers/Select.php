<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;

class Select extends AbstractDisplayer
{
    public static $js = '@select2';
    public static $css = '@select2';

    protected $selector = 'grid-column-select';

    public function display($options = [])
    {
        if ($options instanceof \Closure) {
            $options = $options->call($this, $this->row);
        }

        $this->addScript();

        $optionsHtml = '';

        foreach ($options as $option => $text) {
            $selected = (string) $option === (string) $this->value ? 'selected' : '';
            $optionsHtml .= "<option value=\"$option\" $selected>$text</option>";
        }

        return <<<EOT
<div class="input-group input-group-sm">
    <select style="width: 100%;" class="{$this->selector}" data-key="{$this->getKey()}" data-name="{$this->column->getName()}">
    $optionsHtml
    </select>
</div>
EOT;
    }

    protected function addScript()
    {
        $script = <<<JS
$('.{$this->selector}').off('change').select2().on('change', function(){
    var pk = $(this).data('key'),
        value = $(this).val(),
        name = $(this).data('name'),
        data = {
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
        url: "{$this->resource()}/" + pk,
        type: "POST",
        data: data,
        success: function (data) {
            Dcat.NP.done();
            Dcat.success(data.message);
        }
    });
});
JS;

        Admin::script($script);
    }
}
