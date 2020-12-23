<?php

namespace Dcat\Admin\Http\Actions;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid\RowAction;

class ImportButton extends RowAction
{
    /**
     * @return string
     */
    public function render()
    {
        $button = trans('admin.import');

        $this->setupScript();

        return <<<HTML
<a href="javascript:void(0)" class="import-extension" data-id="{$this->getKey()}">$button</a>
HTML;
    }

    protected function setupScript()
    {
        $text = trans('admin.import_extension_confirm');
        $confirm = trans('admin.confirm');
        $cancel = trans('admin.cancel');

        $url = admin_url('helpers/extensions/import');

        Admin::script(
            <<<JS
$('.import-extension').on('click', function () {
    var id = $(this).data('id'), req;
    if (req) return;
    
    Dcat.confirm("{$text}", '', function () {
        req = 1;
        
        Dcat.loading();
        $.post('$url?id='+id, {}, function (response) {
           Dcat.loading(false);
           req = 0;
        
           if (!response.status) {
               Dcat.error(response.message);
           }
           
           $('#app').prepend('<div class="row"><div class="col-md-12">'+response.content+'</div></div>');
        });
        
    }, "$confirm", "$cancel");
});
JS
        );
    }
}
