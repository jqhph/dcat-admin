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
<li><a href="#" data-name="{$this->parent->getName()}" data-action="batch-delete" data-url="{$this->resource()}">{$this->title}</a></li>
HTML;
    }
}
