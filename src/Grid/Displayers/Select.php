<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;

class Select extends AbstractDisplayer
{
    public function display($options = [])
    {
        if ($options instanceof \Closure) {
            $options = $options->call($this, $this->row);
        }

        $name = $this->column->getName();

        $class = "grid-select-{$name}";

        $script = <<<JS

$('.$class').select2().on('change', function(){
    var pk = $(this).data('key');
    var value = $(this).val();
    LA.NP.start();
    $.ajax({
        url: "{$this->resource()}/" + pk,
        type: "POST",
        data: {
            $name: value,
            _token: LA.token,
            _method: 'PUT'
        },
        success: function (data) {
            LA.NP.done();
            LA.success(data.message);
        }
    });
});

JS;

        Admin::script($script);

        $key = $this->row->{$this->grid->keyName()};

        $optionsHtml = '';

        foreach ($options as $option => $text) {
            $selected = $option == $this->value ? 'selected' : '';
            $optionsHtml .= "<option value=\"$option\" $selected>$text</option>";
        }

        return <<<EOT
<div class="input-group input-group-sm">
    <select style="width: 100%;" class="$class" data-key="$key">
    $optionsHtml
    </select>
</div>
EOT;
    }
}
