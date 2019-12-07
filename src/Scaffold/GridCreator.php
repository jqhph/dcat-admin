<?php

namespace Dcat\Admin\Scaffold;

trait GridCreator
{
    /**
     * @param string $primaryKey
     * @param array  $fields
     *
     * @return string
     */
    protected function generateGrid(string $primaryKey = null, array $fields = [], $timestamps = null)
    {
        $primaryKey = $primaryKey ?: request('primary_key', 'id');
        $fields = $fields ?: request('fields', []);
        $timestamps = $timestamps === null ? request('timestamps', true) : $timestamps;

        $rows = [
            "\$grid->{$primaryKey}->bold()->sortable();",
        ];

        foreach ($fields as $field) {
            if (empty($field['name'])) {
                continue;
            }

            if ($field['name'] == $primaryKey) {
                continue;
            }

            $rows[] = "            \$grid->{$field['name']};";
        }

        if ($timestamps) {
            $rows[] = '            $grid->created_at;';
            $rows[] = '            $grid->updated_at->sortable();';
        }

        $rows[] = <<<EOF
        
            \$grid->filter(function (Grid\Filter \$filter) {
                \$filter->equal('$primaryKey');
        
            });
EOF;

        return implode("\n", $rows);
    }
}
