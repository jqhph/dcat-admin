<?php

namespace Dcat\Admin\Tree\Actions;

use Dcat\Admin\Tree\RowAction;

class Delete extends RowAction
{
    public function html()
    {
        return <<<HTML
<a href="javascript:void(0);" data-message="ID - {$this->getKey()}" data-url="{$this->resource()}/{$this->getKey()}" data-action="delete"><i class="feather icon-trash"></i>&nbsp;</a>
HTML;
    }
}
