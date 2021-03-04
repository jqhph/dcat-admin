<?php

namespace Dcat\Admin\Form\Field;

use Illuminate\Support\Str;

trait CanLoadFields
{
    /**
     * 联动加载.
     *
     * @param string $field
     * @param string $sourceUrl
     * @param string $idField
     * @param string $textField
     *
     * @return $this
     */
    public function load($field, $sourceUrl, string $idField = 'id', string $textField = 'text')
    {
        if (Str::contains($field, '.')) {
            $field = $this->formatName($field);
        }

        $class = $this->normalizeElementClass($field);

        $url = admin_url($sourceUrl);

        return $this->addVariables(['load' => compact('url', 'class', 'idField', 'textField')]);
    }

    /**
     * 联动加载多个字段.
     *
     * @param string $fields
     * @param string $sourceUrls
     * @param string $idField
     * @param string $textField
     *
     * @return $this
     */
    public function loads($fields = [], $sourceUrls = [], string $idField = 'id', string $textField = 'text')
    {
        $fieldsStr = implode('^', array_map(function ($field) {
            if (Str::contains($field, '.')) {
                return $this->normalizeElementClass($field).'_';
            }

            return $this->normalizeElementClass($field);
        }, (array) $fields));
        $urlsStr = implode('^', array_map(function ($url) {
            return admin_url($url);
        }, (array) $sourceUrls));

        return $this->addVariables(['loads' => [
            'fields'    => $fieldsStr,
            'urls'      => $urlsStr,
            'idField'   => $idField,
            'textField' => $textField,
        ]]);
    }
}
