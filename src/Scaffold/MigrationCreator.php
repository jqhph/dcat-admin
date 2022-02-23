<?php

namespace Dcat\Admin\Scaffold;

use Dcat\Admin\Exception\AdminException;
use Illuminate\Database\Migrations\MigrationCreator as BaseMigrationCreator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;

class MigrationCreator extends BaseMigrationCreator
{
    /**
     * @var string
     */
    protected $bluePrint = '';

    /**
     * Create a new migration creator instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Create a new model.
     *
     * @param  string  $name
     * @param  string  $path
     * @param  null  $table
     * @param  bool|true  $create
     * @return string
     */
    public function create($name, $path, $table = null, $create = true)
    {
        $this->ensureMigrationDoesntAlreadyExist($name);

        $path = $this->getPath($name, $path);

        $stub = $this->files->get(__DIR__.'/stubs/create.stub');

        $this->files->put($path, $this->populateAdminStub($name, $stub, $table));
        $this->files->chmod($path, 0777);

        $this->firePostCreateHooks($table, $path);

        return $path;
    }

    /**
     * Populate stub.
     *
     * @param  string  $name
     * @param  string  $stub
     * @param  string  $table
     * @return mixed
     */
    protected function populateAdminStub($name, $stub, $table)
    {
        return str_replace(
            ['DummyClass', 'DummyTable', 'DummyStructure'],
            [$this->getClassName($name), $table, $this->bluePrint],
            $stub
        );
    }

    /**
     * Build the table blueprint.
     *
     * @param  array  $fields
     * @param  string  $keyName
     * @param  bool|true  $useTimestamps
     * @param  bool|false  $softDeletes
     * @return $this
     *
     * @throws \Exception
     */
    public function buildBluePrint($fields = [], $keyName = 'id', $useTimestamps = true, $softDeletes = false)
    {
        $fields = array_filter($fields, function ($field) {
            return isset($field['name']) && ! empty($field['name']);
        });

        if (empty($fields)) {
            throw new AdminException('Table fields can\'t be empty');
        }

        $rows[] = "\$table->increments('$keyName');\n";

        foreach ($fields as $field) {
            $column = "\$table->{$field['type']}('{$field['name']}')";

            if ($field['key']) {
                $column .= "->{$field['key']}()";
            }

            $hasDefault = isset($field['default'])
                && ! is_null($field['default'])
                && $field['default'] !== '';
            if ($hasDefault) {
                $column .= "->default('{$field['default']}')";
            }

            if (Arr::get($field, 'nullable') == 'on') {
                $column .= '->nullable()';
            } elseif (! $hasDefault && $field['type'] === 'string') {
                $column .= "->default('')";
            }

            if (isset($field['comment']) && $field['comment']) {
                $column .= "->comment('{$field['comment']}')";
            }

            $rows[] = $column.";\n";
        }

        if ($useTimestamps) {
            $rows[] = "\$table->timestamps();\n";
        }

        if ($softDeletes) {
            $rows[] = "\$table->softDeletes();\n";
        }

        $this->bluePrint = trim(implode(str_repeat(' ', 12), $rows), "\n");

        return $this;
    }
}
