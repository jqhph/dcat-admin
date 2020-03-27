<?php

namespace Dcat\Admin\Grid\Actions;

use Dcat\Admin\Grid\RowAction;

class Edit extends RowAction
{
    /**
     * @return array|null|string
     */
    public function title()
    {
        return '<i class="feather icon-edit-1"></i> '.__('admin.edit');
    }

    /**
     * @return string
     */
    public function href()
    {
        return "{$this->resource()}/{$this->getKey()}/edit";
    }
}
