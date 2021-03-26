<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Support\Helper;

class MultipleSelect extends Select
{
    protected function formatFieldData($data)
    {
        return Helper::array($this->getValueFromData($data));
    }

    protected function prepareInputValue($value)
    {
        return Helper::array($value, true);
    }
}
