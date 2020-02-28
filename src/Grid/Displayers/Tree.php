<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;

class Tree extends AbstractDisplayer
{
    public function display()
    {
        $this->setupScript();

        $key = $this->key();
        $tableId = $this->grid->tableId();

        $level = $this->grid->model()->getLevelFromRequest();
        $indents = str_repeat(' &nbsp; &nbsp; &nbsp; &nbsp; ', $level);

        return <<<EOT
<a href="javascript:void(0)" class="{$tableId}-grid-load-children" data-level="{$level}" data-inserted="0" data-key="{$key}">
   {$indents}<i class="fa fa-angle-right"></i> &nbsp; {$this->value}
</a>
EOT;
    }

    protected function showNextPage()
    {
        $model = $this->grid->model();

        $showNextPage = $this->grid->allowPagination();
        if (! $model->showAllChildrenNodes() && $showNextPage) {
            $showNextPage =
                $model->getCurrentChildrenPage() < $model->paginator()->lastPage()
                && $model->buildData()->count() == $model->getPerPage();
        }

        return $showNextPage;
    }

    protected function setupScript()
    {
        // 分页问题
        $url = request()->fullUrl();
        $tableId = $this->grid->tableId();

        $model = $this->grid->model();

        // 是否显示下一页按钮
        $pageName = $model->getChildrenPageName(':key');
        $perPage = $model->getPerPage();
        $showNextPage = $model->showAllChildrenNodes() ? 'false' : 'true';

        $script = <<<JS
(function () {
    var req = 0;
    
    $('.{$tableId}-grid-load-children').off('click').click(function () {
        if (req) {
            return;
        }
        
        var key = $(this).data('key'),
            level = $(this).data('level'),
            trClass = '{$tableId}-tr-'+key,
            data = {
                    _token: LA.token, 
                    '{$model->getParentIdQueryName()}': key, 
                    '{$model->getLevelQueryName()}': level + 1, 
                };
        
         $('.'+trClass).toggle();
    
        if ($(this).data('inserted') == '0') {
            var row = $(this).closest('tr');
            request(1);
            $(this).data('inserted', 1);
        }
       
        $("i", this).toggleClass("fa-angle-right fa-angle-down");
        
        function request(page, after) {
            if (req) {
                return;
            }
            req = 1;
             LA.loading();
             
             data['{$pageName}'.replace(':key', key)] = page;
           
            $.ajax({
                url: '$url',
                type: 'GET',
                data: data,
                cache: false,
                headers: {'X-PJAX': true},
                success: function (resp) {
                    after && after();
                    LA.loading(false);
                    req = 0;
                    
                    // 获取最后一行
                    var children = $('.'+trClass);
                    row = children.length ? children.last() : row;
                    
                    var _body = $('<div>'+resp+'</div>'),
                        _tbody = _body.find('#{$tableId} tbody'),
                        lastPage = _body.find('last-page').text(),
                        nextPage = _body.find('next-page').text();
  
                    // 标记子节点行
                    _tbody.find('tr').each(function (_, v) {
                        $(v).addClass(trClass);
                        $(v).attr('data-level', level + 1) 
                    });
                    
                    var html = _tbody.html(),
                        icon = '<svg style="fill:currentColor" t="1582877365167" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="32874" width="24" height="24"><path d="M162.8 515m-98.3 0a98.3 98.3 0 1 0 196.6 0 98.3 98.3 0 1 0-196.6 0Z" p-id="32875"></path><path d="M511.9 515m-98.3 0a98.3 98.3 0 1 0 196.6 0 98.3 98.3 0 1 0-196.6 0Z" p-id="32876"></path><path d="M762.8 515a98.3 98.3 0 1 0 196.6 0 98.3 98.3 0 1 0-196.6 0Z" p-id="32877"></path></svg>';
                    
                    if ({$showNextPage} && _tbody.find('tr').length == '{$perPage}' && lastPage >= page) {
                        // 加载更多
                        html += "<tr data-page='"+nextPage+"' class='{$tableId}-load-next-"+key+" "
                            +trClass+"'><td colspan='"+(row.find('td').length)
                            +"' align='center' style='cursor: pointer'> <a>"+icon+"</a> </td></tr>";
                    }
                    
                    // 附加子节点
                    row.after(html);

                     // 加载更多
                    $('.{$tableId}-load-next-'+key).off('click').click(function () {
                        var _t = $(this);
                        request(_t.data('page'), function () {
                            _t.remove();
                        });
                    });
                   
                    // 附加子节点js脚本以及触发子节点js脚本执行
                    _body.find('script').each(function (_, v) {
                        row.after(v);
                    });
                    $(document).trigger('pjax:script')
                },
                error:function(a, b, c){
                    after && after();
                    LA.loading(false);
                    req = 0;
                    if (a.status != 404) {
                        LA.ajaxError(a, b, c)
                    }
                }
            });
        }   
    });
})();
JS;
        Admin::script($script);
    }
}
