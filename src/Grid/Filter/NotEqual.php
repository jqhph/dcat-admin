<?php

namespace Dcat\Admin\Grid\Filter;

use Illuminate\Support\Arr;

class NotEqual extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function condition($inputs)
    {
        $value = Arr::get($inputs, $this->column);

        if ($value === null) {
            return;
        }

        $this->value = $value;

        return $this->buildCondition($this->column, '!=', $this->value);
    }
}
