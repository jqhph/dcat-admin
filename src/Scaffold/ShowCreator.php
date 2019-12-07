<?php

namespace Dcat\Admin\Scaffold;

trait ShowCreator
{
    /**
     * @param string $primaryKey
     * @param array  $fields
     *
     * @return string
     */
    protected function generateShow(string $primaryKey = null, array $fields = [], $timestamps = null)
    {
        $primaryKey = $primaryKey ?: request('primary_key', 'id');
        $fields = $fields ?: request('fields', []);
        $timestamps = $timestamps === null ? request('timestamps', true) : $timestamps;

        $rows = [];

        if ($primaryKey) {
            $rows[] = "            \$show->{$primaryKey};";
        }

        foreach ($fields as $k => $field) {
            if (empty($field['name'])) {
                continue;
            }

            $rows[] = "            \$show->{$field['name']};";

            if ($k === 1 && (count($fields) > 2 || $timestamps)) {
                $rows[] = '            $show->divider();';
            }
        }

        if ($timestamps) {
            $rows[] = '            $show->created_at;';
            $rows[] = '            $show->updated_at;';
        }

        return trim(implode("\n", $rows));
    }
}
