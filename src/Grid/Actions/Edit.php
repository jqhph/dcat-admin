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
        return __('admin.edit');
    }

    /**
     * @return string
     */
    public function href()
    {
        return "{$this->resource()}/{$this->key()}/edit";
    }
}
