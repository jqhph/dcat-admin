<?php

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Illuminate\Contracts\Support\Renderable;

class ExportButton implements Renderable
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * Create a new Export button instance.
     *
     * @param  Grid  $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * Set up script for export button.
     */
    protected function setUpScripts()
    {
        $script = <<<JS
$('.{$this->grid->getExportSelectedName()}').on('click', function (e) {
    e.preventDefault();
    
    var rows = Dcat.grid.selected('{$this->grid->getName()}').join(',');
    if (! rows) {
        return false;
    }
    
    var href = $(this).attr('href').replace('__rows__', rows);
    location.href = href;
});
JS;

        Admin::script($script);
    }

    /**
     * @return string|void
     */
    protected function renderExportAll()
    {
        if (! $this->grid->exporter()->option('show_export_all')) {
            return;
        }
        $all = trans('admin.all');

        return "<li class='dropdown-item'><a href=\"{$this->grid->exportUrl('all')}\" target=\"_blank\">{$all}</a></li>";
    }

    /**
     * @return string
     */
    protected function renderExportCurrentPage()
    {
        if (! $this->grid->exporter()->option('show_export_current_page')) {
            return;
        }

        $page = $this->grid->model()->getCurrentPage() ?: 1;
        $currentPage = trans('admin.current_page');

        return "<li class='dropdown-item'><a href=\"{$this->grid->exportUrl('page', $page)}\" target=\"_blank\">{$currentPage}</a></li>";
    }

    /**
     * @return string|void
     */
    protected function renderExportSelectedRows()
    {
        if (
            ! $this->grid->option('row_selector')
            || ! $this->grid->exporter()->option('show_export_selected_rows')
        ) {
            return;
        }

        $selectedRows = trans('admin.selected_rows');

        return "<li class='dropdown-item'><a href=\"{$this->grid->exportUrl('selected', '__rows__')}\" target=\"_blank\" class='{$this->grid->getExportSelectedName()}'>{$selectedRows}</a></li>";
    }

    /**
     * Render Export button.
     *
     * @return string
     */
    public function render()
    {
        $this->setUpScripts();

        $export = trans('admin.export');

        return $this->grid->tools()->format(
            <<<EOT
<div class="btn-group dropdown" style="margin-right:3px">
    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
        <i class="feather icon-download"></i>
        <span class="d-none d-sm-inline">&nbsp;{$export}&nbsp;</span>
        <span class="caret"></span>
        <span class="sr-only"></span>
    </button>
    <ul class="dropdown-menu" role="menu">
        {$this->renderExportAll()}
        {$this->renderExportCurrentPage()}
        {$this->renderExportSelectedRows()}
    </ul>
</div>
EOT
        );
    }
}
