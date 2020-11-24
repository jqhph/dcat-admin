<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;

class Select extends AbstractDisplayer
{
    public static $js = '@select2';
    public static $css = '@select2';

    protected $selector = 'grid-column-select';

    public function display($options = [], $refresh = false)
    {
        if ($options instanceof \Closure) {
            $options = $options->call($this, $this->row);
        }

        $this->addScript($refresh);

        $optionsHtml = '';

        foreach ($options as $option => $text) {
            $selected = (string) $option === (string) $this->value ? 'selected' : '';
            $optionsHtml .= "<option value=\"$option\" $selected>$text</option>";
        }

        return <<<EOT
<div class="input-group input-group-sm">
    <select style="width: 100%;" class="{$this->selector}" data-url="{$this->url()}" data-name="{$this->column->getName()}">
    $optionsHtml
    </select>
</div>
EOT;
    }

    protected function url()
    {
        return $this->resource().'/'.$this->getKey();
    }

    protected function addScript($refresh)
    {
        $script = <<<JS
$('.{$this->selector}').off('change').select2().on('change', function(){
    var value = $(this).val(),
        name = $(this).data('name'),
        url = $(this).data('url'),
        data = {
            _method: 'PUT'
        },
        reload = '{$refresh}';
    
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
            Dcat.NP.done();
            Dcat.success(data.message);
            reload && Dcat.reload();
        }
    });
});
JS;

        Admin::script($script);
    }
}
