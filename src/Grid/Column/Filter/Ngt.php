<?php

namespace Dcat\Admin\Grid\Column\Filter;

use Dcat\Admin\Grid\Model;

class Ngt extends Equal
{
    /**
     * Add a binding to the query.
     *
     * @param string     $value
     * @param Model|null $model
     */
    public function addBinding($value, Model $model)
    {
        $value = trim($value);
        if (empty($value)) {
            return;
        }

        $model->where($this->columnName(), '<=', $value);
    }
}
