<?php

namespace Dcat\Admin\Grid\Filter;

use Illuminate\Support\Arr;

class FindInSet extends AbstractFilter
{
    /**
     * Input value from presenter.
     *
     * @var mixed
     */
    public $input;

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

        $this->input = $this->value = $value;

        $query = function ($query) {
            $query->whereRaw("FIND_IN_SET(?, $this->column)", $this->value);
        };

        return $this->buildCondition($query->bindTo($this));
    }
}
