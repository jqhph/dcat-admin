<?php

namespace Dcat\Admin\Form\Field;

trait HasDepends
{
    /**
     * 联动加载多个字段.
     *
     * @param  array|string  $fields
     * @param  bool  $clear
     * @return $this
     */
    public function depends($fields = [], bool $clear = true)
    {
        $fields = array_map(function ($field) {
            return $this->formatName($field);
        }, (array) $fields);

        return $this->addVariables([
            'depends' => [
                'fields' => json_encode($fields, \JSON_UNESCAPED_UNICODE),
                'clear' => $clear,
            ],
        ]);
    }
}
