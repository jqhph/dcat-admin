<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;

/**
 * Class QRCode.
 */
class QRCode extends AbstractDisplayer
{
    protected function addScript()
    {
        $script = <<<'JS'
$('.grid-column-qrcode').popover({
    html: true,
    trigger: 'focus'
});
JS;
        Admin::script($script);
    }

    public function display($formatter = null, $width = 150, $height = 150)
    {
        $this->addScript();

        $content = $this->column->getOriginal();

        if ($formatter instanceof \Closure) {
            $formatter->bindTo($this->row);

            $content = call_user_func($formatter, $content);
        }

        $img = "<img src='https://api.qrserver.com/v1/create-qr-code/?size={$width}x{$height}&data={$content}' style='height: {$width}px;width: {$height}px;'/>";

        return <<<HTML
<a href="javascript:void(0);" class="grid-column-qrcode text-muted" data-content="{$img}" data-toggle='popover' tabindex='0'>
    <i class="fa fa-qrcode"></i>
</a>&nbsp;{$this->value}
HTML;
    }
}
