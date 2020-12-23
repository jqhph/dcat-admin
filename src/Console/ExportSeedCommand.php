<?php

namespace Dcat\Admin\Console;

use Illuminate\Console\Command;

class ExportSeedCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'admin:export-seed {classname=AdminTablesSeeder}
                                              {--users : add to seed users tables}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export seed a dcat-admin database tables menu, roles and permissions';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $name = $this->argument('classname');
        $exceptFields = [];
        $exportUsers = $this->option('users');

        $namespace = version_compare(app()->version(), '8.0.0', '<') ? 'seeds' : 'seeders';

        $seedFile = $this->laravel->databasePath().'/'.$namespace.'/'.$name.'.php';
        $contents = $this->getStub('AdminTablesSeeder');

        $replaces = [
            'DummyNamespace' => ucwords($namespace),
            'DummyClass' => $name,

            'ClassMenu'             => $this->getTableName('admin.database.menu_model'),
            'ClassPermission'       => $this->getTableName('admin.database.permissions_model'),
            'ClassRole'             => $this->getTableName('admin.database.roles_model'),
            'ClassSetting'          => 'Models\Setting',
            'ClassExtension'        => 'Models\Extension',
            'ClassExtensionHistory' => 'Models\ExtensionHistory',

            'TablePermissionMenu'  => $this->getTableName('admin.database.permission_menu_table'),
            'TableRoleMenu'        => $this->getTableName('admin.database.role_menu_table'),
            'TableRolePermissions' => $this->getTableName('admin.database.role_permissions_table'),

            'ArrayMenu'       => $this->getTableDataArrayAsString(config('admin.database.menu_table'), $exceptFields),
            'ArrayPermission' => $this->getTableDataArrayAsString(config('admin.database.permissions_table'), $exceptFields),
            'ArrayRole'       => $this->getTableDataArrayAsString(config('admin.database.roles_table'), $exceptFields),
            'ArraySetting'    => $this->getTableDataArrayAsString(config('admin.database.settings_table') ?: 'admin_settings', $exceptFields),
            'ArrayExtension'  => $this->getTableDataArrayAsString(config('admin.database.extensions_table') ?: 'admin_extensions', $exceptFields),
            'ArrayExtHistory' => $this->getTableDataArrayAsString(config('admin.database.extension_histories_table') ?: 'admin_extension_histories', $exceptFields),

            'ArrayPivotPermissionMenu'  => $this->getTableDataArrayAsString(config('admin.database.permission_menu_table'), $exceptFields),
            'ArrayPivotRoleMenu'        => $this->getTableDataArrayAsString(config('admin.database.role_menu_table'), $exceptFields),
            'ArrayPivotRolePermissions' => $this->getTableDataArrayAsString(config('admin.database.role_permissions_table'), $exceptFields),
        ];

        if ($exportUsers) {
            $replaces = array_merge($replaces, [
                'ClassUsers'            => $this->getTableName('admin.database.users_model'),
                'TableRoleUsers'        => config('admin.database.role_users_table'),
                'ArrayUsers'            => $this->getTableDataArrayAsString(config('admin.database.users_table'), $exceptFields),
                'ArrayPivotRoleUsers'   => $this->getTableDataArrayAsString(config('admin.database.role_users_table'), $exceptFields),
            ]);
        } else {
            $contents = preg_replace('/\/\/ users tables[\s\S]*?(?=\/\/ finish)/mu', '', $contents);
        }

        $contents = str_replace(array_keys($replaces), array_values($replaces), $contents);

        $this->laravel['files']->put($seedFile, $contents);

        $this->line('<info>Admin tables seed file was created:</info> '.str_replace(base_path(), '', $seedFile));
        $this->line("Use: <info>php artisan db:seed --class={$name}</info>");
    }

    protected function getTableName($config)
    {
        return trim(str_replace('Dcat\\Admin\\', '', config($config)), '\\');
    }

    /**
     * Get data array from table as string result var_export.
     *
     * @param string $table
     * @param array  $exceptFields
     *
     * @return string
     */
    protected function getTableDataArrayAsString($table, $exceptFields = [])
    {
        $fields = \DB::getSchemaBuilder()->getColumnListing($table);
        $fields = array_diff($fields, $exceptFields);

        $array = \DB::table($table)->get($fields)->map(function ($item) {
            return (array) $item;
        })->all();

        return $this->varExport($array, str_repeat(' ', 12));
    }

    /**
     * Get stub contents.
     *
     * @param $name
     *
     * @return string
     */
    protected function getStub($name)
    {
        return $this->laravel['files']->get(__DIR__."/stubs/$name.stub");
    }

    /**
     * Custom var_export for correct work with \r\n.
     *
     * @param $var
     * @param string $indent
     *
     * @return string
     */
    protected function varExport($var, $indent = '')
    {
        switch (gettype($var)) {

            case 'string':
                return '"'.addcslashes($var, "\\\$\"\r\n\t\v\f").'"';

            case 'array':
                $indexed = array_keys($var) === range(0, count($var) - 1);

                $r = [];

                foreach ($var as $key => $value) {
                    $r[] = "$indent    "
                        .($indexed ? '' : $this->varExport($key).' => ')
                        .$this->varExport($value, "{$indent}    ");
                }

                return "[\n".implode(",\n", $r)."\n".$indent.']';

            case 'boolean':
                return $var ? 'true' : 'false';

            case 'integer':
            case 'double':
                return $var;

            default:
                return var_export($var, true);
        }
    }
}
