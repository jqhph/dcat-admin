<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;

/**
 * Class Copyable.
 *
 * @see https://codepen.io/shaikmaqsood/pen/XmydxJ
 */
class Copyable extends AbstractDisplayer
{
    protected function addScript()
    {
        $script = <<<'JS'
$('.grid-column-copyable').off('click').on('click', function (e) {
    
    var content = $(this).data('content');
    
    var $temp = $('<input>');
    
    $("body").append($temp);
    $temp.val(content).select();
    document.execCommand("copy");
    $temp.remove();
    
    $(this).tooltip('show');
});
JS;
        Admin::script($script);
    }

    public function display()
    {
        $this->addScript();

        $this->value = Helper::htmlEntityEncode($this->value);

        $html = <<<HTML
<a href="javascript:void(0);" class="grid-column-copyable text-muted" data-content="{$this->value}" title="{$this->trans('copied')}" data-placement="bottom">
    <i class="fa fa-copy"></i>
</a>&nbsp;{$this->value}
HTML;

        return $this->value === '' || $this->value === null ? $this->value : $html;
    }
}
