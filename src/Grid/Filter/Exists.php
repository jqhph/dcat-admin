<?php

namespace Dcat\Admin\Grid\Filter;

use Illuminate\Support\Arr;

class Exists extends AbstractFilter
{
    /**
     * Get condition of this filter.
     *
     * @param  array  $inputs
     * @return array|mixed|void
     */
    public function condition($inputs)
    {
        if (!Arr::has($inputs, $this->column)) {
            return;
        }

        $this->value = Arr::get($inputs, $this->column);

        if ($this->value === "0") {
            $this->query = 'whereNull';
            return $this->buildCondition($this->column);
        }

        $this->query = 'whereNotNull';
        return $this->buildCondition($this->column);
    }
}
