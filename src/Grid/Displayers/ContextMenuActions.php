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
        parent::addScript();
        $script = <<<JS
(function () {
    $("body").on("contextmenu", "table#grid-table tr", function(e) {
         $('#grid-context-menu .dropdown-menu').hide();
        
         var menu = $(this).find('td .grid-dropdown-actions .dropdown-menu');
         console.log(menu.html());
         var index = $(this).index();
         
         if (menu.length) {
             menu.attr('index', index).detach().appendTo('#grid-context-menu');
         } else {
             menu = $('#grid-context-menu .dropdown-menu[index='+index+']');
         }
         
         var height = 0;

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
        Admin::html('<div id="grid-context-menu"></div>');
        Admin::style('.column-__actions__ {display: none !important;}');

        return parent::display($callback);
    }
}
