<?php

namespace Dcat\Admin\Grid\Tools;

class BatchDelete extends BatchAction
{
    public function __construct($title)
    {
        $this->title = $title;
    }

    public function render()
    {
        return <<<HTML
<li><a href="#" data-method="{$this->grid->selectedRowsName()}" data-action="batch-delete" data-url="{$this->resource}">{$this->title}</a></li>
HTML;
    }

    /**
     * Script of batch delete action.
     */
    public function script()
    {
    }
}
