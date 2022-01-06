<?php

namespace Dcat\Admin\Grid\Column\Filter;

use Dcat\Admin\Grid\Model;

class StartWith extends Equal
{
    /**
     * Add a binding to the query.
     *
     * @param  string  $value
     * @param  Model|null  $model
     */
    public function addBinding($value, Model $model)
    {
        $value = trim($value);
        if ($value === '') {
            return;
        }

        $this->withQuery($model, 'where', ['like', "{$value}%"]);
    }
}
