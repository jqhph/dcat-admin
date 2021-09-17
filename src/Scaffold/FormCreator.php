<?php

namespace Dcat\Admin\Scaffold;

trait FormCreator
{
    /**
     * @param  string  $primaryKey
     * @param  array  $fields
     * @param  bool  $timestamps
     * @return string
     */
    protected function generateForm(string $primaryKey = null, array $fields = [], $timestamps = null)
    {
        $primaryKey = $primaryKey ?: request('primary_key', 'id');
        $fields = $fields ?: request('fields', []);
        $timestamps = $timestamps === null ? request('timestamps') : $timestamps;

        $rows = [
            <<<EOF
\$form->display('{$primaryKey}');
EOF

        ];

        foreach ($fields as $field) {
            if (empty($field['name'])) {
                continue;
            }

            if ($field['name'] == $primaryKey) {
                continue;
            }

            $rows[] = "            \$form->text('{$field['name']}');";
        }
        if ($timestamps) {
            $rows[] = <<<'EOF'
        
            $form->display('created_at');
            $form->display('updated_at');
EOF;
        }

        return implode("\n", $rows);
    }
}
