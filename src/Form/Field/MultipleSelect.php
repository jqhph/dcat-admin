<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Support\Helper;
use Illuminate\Support\Arr;

class MultipleSelect extends Select
{
    protected function formatFieldData($data)
    {
        return Helper::array(Arr::get($data, $this->normalizeColumn()));
    }

    protected function prepareInputValue($value)
    {
        return Helper::array($value, true);
    }
}
