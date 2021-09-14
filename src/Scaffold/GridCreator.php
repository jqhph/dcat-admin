<?php

namespace Dcat\Admin\Scaffold;

trait GridCreator
{
    /**
     * @param  string  $primaryKey
     * @param  array  $fields
     * @return string
     */
    protected function generateGrid(string $primaryKey = null, array $fields = [], $timestamps = null)
    {
        $primaryKey = $primaryKey ?: request('primary_key', 'id');
        $fields = $fields ?: request('fields', []);
        $timestamps = $timestamps === null ? request('timestamps') : $timestamps;

        $rows = [
            "\$grid->column('{$primaryKey}')->sortable();",
        ];

        foreach ($fields as $field) {
            if (empty($field['name'])) {
                continue;
            }

            if ($field['name'] == $primaryKey) {
                continue;
            }

            $rows[] = "            \$grid->column('{$field['name']}');";
        }

        if ($timestamps) {
            $rows[] = '            $grid->column(\'created_at\');';
            $rows[] = '            $grid->column(\'updated_at\')->sortable();';
        }

        $rows[] = <<<EOF
        
            \$grid->filter(function (Grid\Filter \$filter) {
                \$filter->equal('$primaryKey');
        
            });
EOF;

        return implode("\n", $rows);
    }
}
