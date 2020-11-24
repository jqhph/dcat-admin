<?php

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Grid\BatchAction;

class BatchDelete extends BatchAction
{
    public function __construct($title)
    {
        $this->title = $title;
    }

    public function render()
    {
        return <<<HTML
<a href="#" data-name="{$this->parent->getName()}" data-action="batch-delete" data-url="{$this->resource()}"><i class="feather icon-trash"></i> {$this->title}</a>
HTML;
    }
}
