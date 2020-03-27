<?php

namespace Dcat\Admin\Grid\Actions;

use Dcat\Admin\Grid\RowAction;

class Show extends RowAction
{
    /**
     * @return array|null|string
     */
    public function title()
    {
        return '<i class="feather icon-eye"></i> '.__('admin.show');
    }

    /**
     * @return string
     */
    public function href()
    {
        return "{$this->resource()}/{$this->getKey()}";
    }
}
