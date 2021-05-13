<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;

class ContextMenuActions extends DropdownActions
{
    protected $elementId = 'grid-context-menu';

    protected function addScript()
    {
        $script = <<<JS
(function () {
    var id = '#{$this->elementId}';

    $("body").on("contextmenu", "#{$this->grid->getTableId()} tr", function(e) {
         $(id + ' .dropdown-menu').hide();

         var menu = $(this).find('td .grid-dropdown-actions .dropdown-menu');
         var index = $(this).index();

         if (menu.length) {
             menu.attr('index', index).detach().appendTo(id);
         } else {
             menu = $(id + ' .dropdown-menu[index='+index+']');
         }

         if (menu.height() > (document.body.clientHeight - e.pageY)) {
            menu.css({left: e.pageX+10, top: e.pageY - menu.height()}).show();
         } else {
            menu.css({left: e.pageX+10, top: e.pageY-10}).show();
         }
        return false;
    });

    if (! $(id).length) {
        $("body").append('<div id="{$this->elementId}" class="dropdown" style="display: contents"></div>');
    }

    $(document).on('click',function(){
        $(id + ' .dropdown-menu').hide();
    })

    $(id).click('a', function () {
        $(this).find('.dropdown-menu').hide();
    });
})();
JS;

        Admin::script($script);
    }

    public function display($callback = null)
    {
        $this->addScript();

        Admin::style('.grid__actions__ .dropdown{display: none!important;} th.grid__actions__{display: none!important;} .grid__actions__{width:1px}');

        return parent::display($callback);
    }
}
