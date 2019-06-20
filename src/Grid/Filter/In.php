<?php

namespace Dcat\Admin\Grid\Filter;

use Illuminate\Support\Arr;

class In extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    protected $query = 'whereIn';

    /**
     * Get condition of this filter.
     *
     * @param array $inputs
     *
     * @return mixed
     */
    public function condition($inputs)
    {
        $value = Arr::get($inputs, $this->column);

        if ($value === null) {
            return;
        }

        $this->value = is_array($value) ? $value : explode(',', $value);

        return $this->buildCondition($this->column, $this->value);
    }
}
