<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;

class Tree extends AbstractDisplayer
{
    protected static $js = [
        '@grid-extension',
    ];

    protected function setupScript()
    {
        $tableId = $this->grid->getTableId();

        $model = $this->grid->model();

        // 是否显示下一页按钮
        $pageName = $model->getChildrenPageName(':key');
        $showNextPage = $model->showAllChildrenNodes() ? 'false' : 'true';

        $script = <<<JS
Dcat.grid.Tree({
    button: '.{$tableId}-grid-load-children',
    table: '#{$tableId}',
    url: '{$model->generateTreeUrl()}',
    perPage: '{$model->getPerPage()}',
    showNextPage: {$showNextPage},
    pageQueryName: '{$pageName}',
    parentIdQueryName: '{$model->getParentIdQueryName()}',
    depthQueryName: '{$model->getDepthQueryName()}',
});
JS;
        Admin::script($script);
    }

    public function display()
    {
        $this->setupScript();

        $key = $this->getKey();
        $tableId = $this->grid->getTableId();

        $depth = $this->grid->model()->getDepthFromRequest();
        $indents = str_repeat(' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ', $depth);

        return <<<EOT
<a href="javascript:void(0)" class="{$tableId}-grid-load-children" data-depth="{$depth}" data-inserted="0" data-key="{$key}">
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
}
