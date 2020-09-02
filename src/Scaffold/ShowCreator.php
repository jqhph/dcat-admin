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
        $timestamps = $timestamps === null ? request('timestamps') : $timestamps;

        $rows = [];

        if ($primaryKey) {
            $rows[] = "            \$show->field('{$primaryKey}');";
        }

        foreach ($fields as $k => $field) {
            if (empty($field['name'])) {
                continue;
            }

            $rows[] = "            \$show->field('{$field['name']}');";

//            if ($k === 1 && (count($fields) > 2 || $timestamps)) {
//                $rows[] = '            $show->divider();';
//            }
        }

        if ($timestamps) {
            $rows[] = '            $show->field(\'created_at\');';
            $rows[] = '            $show->field(\'updated_at\');';
        }

        return trim(implode("\n", $rows));
    }
}
