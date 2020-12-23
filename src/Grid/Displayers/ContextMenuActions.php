<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;

class ContextMenuActions extends DropdownActions
{
    /**
     * {@inheritdoc}
     */
    protected function addScript()
    {
        $script = <<<JS
(function () {
    $("body").on("contextmenu", "table#{$this->grid->getTableId()} tr", function(e) {
         $('#grid-context-menu .dropdown-menu').hide();
        
         var menu = $(this).find('td .grid-dropdown-actions .dropdown-menu');
         var index = $(this).index();
         
         if (menu.length) {
             menu.attr('index', index).detach().appendTo('#grid-context-menu');
         } else {
             menu = $('#grid-context-menu .dropdown-menu[index='+index+']');
         }
         
         if (menu.height() > (document.body.clientHeight - e.pageY)) {
            menu.css({left: e.pageX+10, top: e.pageY - menu.height()}).show();
         } else {
            menu.css({left: e.pageX+10, top: e.pageY-10}).show();
         }
        return false;
    });
    
    $(document).on('click',function(){
        $('#grid-context-menu .dropdown-menu').hide();
    })
   
    $('#grid-context-menu').click('a', function () {
        $('#grid-context-menu .dropdown-menu').hide();
    });
})();
JS;

        Admin::script($script);
    }

    /**
     * {@inheritdoc}
     */
    public function display($callback = null)
    {
        $this->addScript();

        Admin::html('<div id="grid-context-menu" class="dropdown" style="display: contents"></div>');
        Admin::style('.grid__actions__ .dropdown{display: none!important;} th.grid__actions__{display: none!important;} .grid__actions__{width:1px}');

        return parent::display($callback);
    }
}
