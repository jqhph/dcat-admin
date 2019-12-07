<?php

namespace Dcat\Admin\Extension\Grid;

use Dcat\Admin\Admin;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Fluent;

class ImportButton implements Renderable
{
    /**
     * @var Fluent
     */
    protected $row;

    public function __construct($row)
    {
        $this->row = $row;
    }

    /**
     * @return string
     */
    public function render()
    {
        $button = trans('admin.import');

        $this->setupScript();

        return <<<HTML
<a href="javascript:void(0)" class="import-extension" data-id="{$this->row->id}">$button</a>
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
$('.import-extension').click(function () {
    var id = $(this).data('id'), req;
    if (req) return;
    
    LA.confirm("{$text}", function () {
        var url = '$url';
        req = 1;
        
        LA.loading();
        $.post('$url?id='+id, {
            _token: LA.token,
        }, function (response) {
           LA.loading(false);
           req = 0;
        
           if (!response.status) {
               LA.error(response.message);
           }
           
           $('.content').prepend('<div class="row"><div class="col-md-12">'+response.content+'</div></div>');
        });
        
    }, "$confirm", "$cancel");
});
JS
        );
    }
}
