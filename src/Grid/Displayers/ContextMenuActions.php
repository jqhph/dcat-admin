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
    // 先解绑事件再绑定，不然切换界面时会导致事件重复绑定
    $("body").off('contextmenu').on("contextmenu", "#{$this->grid->getTableId()} tr", function(e) {
        // 每次都要移除，免得不同界面出现Bug
         $(id + ' .dropdown-menu').remove();

         // 直接复制操作栏元素和事件
         var menu = $(this).find('td .grid-dropdown-actions .dropdown-menu').clone(true);
         var index = $(this).index();

         menu.attr('index', index).appendTo(id);

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
        // 每次都需要移除插入的元素
        $(id + ' .dropdown-menu').remove();
    })

    $(id).click('a', function () {
        // 每次都需要移除插入的元素
        $(this).find('.dropdown-menu').remove();
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
