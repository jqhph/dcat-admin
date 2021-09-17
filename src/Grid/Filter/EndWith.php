<?php

namespace Dcat\Admin\Grid\Filter;

use Illuminate\Support\Arr;

class EndWith extends AbstractFilter
{
    protected $type = 'like';

    /**
     * Get condition of this filter.
     *
     * @param  array  $inputs
     * @return array|mixed|void
     */
    public function condition($inputs)
    {
        $value = Arr::get($inputs, $this->column);

        if ($value === null) {
            return;
        }

        $this->value = $value;

        return $this->buildCondition($this->column, $this->type, "%{$this->value}");
    }

    public function ilike()
    {
        $this->type = 'ilike';

        return $this;
    }
}
