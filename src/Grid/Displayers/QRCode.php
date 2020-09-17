<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;

/**
 * Class QRCode.
 */
class QRCode extends AbstractDisplayer
{
    protected static $js = [
        '@qrcode',
    ];

    protected function addScript()
    {
        $script = <<<'JS'
$('.grid-column-qrcode').on('click', function () {
    var $this = $(this), data = $this.data();
    data.render = 'image';
    $this.qrcode(data);
    
    var img = $this.find('img');
    
    $this.attr('data-content', '<img width="'+data.width+'" height="'+data.height+'" src="'+img.attr('src')+'">');
    img.remove();
    
    $this.popover('show')
});
JS;
        Admin::script($script);
    }

    public function display($formatter = null, $width = 150, $height = 150)
    {
        $this->addScript();

        $content = $this->column->getOriginal();

        if ($formatter instanceof \Closure) {
            $content = $formatter->call($this->row, $content);
        }

        return <<<HTML
<a href="javascript:void(0);" 
    class="grid-column-qrcode text-muted" 
    data-text="{$content}" 
    data-width="{$width}"
    data-height="{$height}"
    data-trigger="trigger" 
    data-html="true" 
    data-toggle='popover' 
    tabindex='0'
>
    <i class="fa fa-qrcode"></i>
</a>&nbsp;{$this->value}
HTML;
    }
}
