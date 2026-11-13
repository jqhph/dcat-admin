<?php

namespace Dcat\Admin\Http\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Http\Auth\Permission;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Scaffold\ControllerCreator;
use Dcat\Admin\Scaffold\LangCreator;
use Dcat\Admin\Scaffold\MigrationCreator;
use Dcat\Admin\Scaffold\ModelCreator;
use Dcat\Admin\Scaffold\RepositoryCreator;
use Dcat\Admin\Support\Helper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

class ScaffoldController extends Controller
{
    public static $dbTypes = [
        'string', 'integer', 'text', 'float', 'double', 'decimal', 'boolean', 'date', 'time',
        'dateTime', 'timestamp', 'char', 'mediumText', 'longText', 'tinyInteger', 'smallInteger',
        'mediumInteger', 'bigInteger', 'unsignedTinyInteger', 'unsignedSmallInteger', 'unsignedMediumInteger',
        'unsignedInteger', 'unsignedBigInteger', 'enum', 'json', 'jsonb', 'dateTimeTz', 'timeTz',
        'timestampTz', 'nullableTimestamps', 'binary', 'ipAddress', 'macAddress',
    ];

    public static $dataTypeMap = [
        'int'                => 'integer',
        'int@unsigned'       => 'unsignedInteger',
        'tinyint'            => 'tinyInteger',
        'tinyint@unsigned'   => 'unsignedTinyInteger',
        'smallint'           => 'smallInteger',
        'smallint@unsigned'  => 'unsignedSmallInteger',
        'mediumint'          => 'mediumInteger',
        'mediumint@unsigned' => 'unsignedMediumInteger',
        'bigint'             => 'bigInteger',
        'bigint@unsigned'    => 'unsignedBigInteger',

        'date'      => 'date',
        'time'      => 'time',
        'datetime'  => 'dateTime',
        'timestamp' => 'timestamp',

        'enum'   => 'enum',
        'json'   => 'json',
        'binary' => 'binary',

        'float'   => 'float',
        'double'  => 'double',
        'decimal' => 'decimal',

        'varchar'    => 'string',
        'char'       => 'char',
        'text'       => 'text',
        'mediumtext' => 'mediumText',
        'longtext'   => 'longText',
    ];

    public function index(Content $content)
    {
        if (! config('app.debug')) {
            Permission::error();
        }

        if ($tableName = request('singular')) {
            return $this->singular($tableName);
        }

        Admin::requireAssets('select2');
        Admin::requireAssets('sortable');

        $dbTypes = static::$dbTypes;
        $dataTypeMap = static::$dataTypeMap;
        $action = URL::current();
        $namespaceBase = 'App\\'.implode('\\', array_map(function ($name) {
            return Str::studly($name);
        }, explode(DIRECTORY_SEPARATOR, substr(config('admin.directory'), strlen(app_path().DIRECTORY_SEPARATOR)))));
        $tables = collect($this->getDatabaseColumns())->map(function ($v) {
            return array_keys($v);
        })->toArray();

        return $content
            ->title(trans('admin.scaffold.header'))
            ->description(' ')
            ->body(view(
                'admin::helpers.scaffold',
                compact('dbTypes', 'action', 'tables', 'dataTypeMap', 'namespaceBase')
            ));
    }

    protected function singular($tableName)
    {
        return [
            'status' => 1,
            'value'  => Str::singular($tableName),
        ];
    }

    public function store(Request $request)
    {
        if (! config('app.debug')) {
            Permission::error();
        }

        $paths = [];
        $message = '';

        $creates = (array) $request->get('create');
        $table = Helper::slug($request->get('table_name'), '_');
        $controller = $request->get('controller_name');
        $model = $request->get('model_name');
        $repository = $request->get('repository_name');

        try {
            // 1. Create model.
            if (in_array('model', $creates)) {
                $modelCreator = new ModelCreator($table, $model);

                $paths['model'] = $modelCreator->create(
                    $request->get('primary_key'),
                    $request->get('timestamps') == 1,
                    $request->get('soft_deletes') == 1
                );
            }

            // 2. Create controller.
            if (in_array('controller', $creates)) {
                $paths['controller'] = (new ControllerCreator($controller))
                    ->create(in_array('repository', $creates) ? $repository : $model);
            }

            // 3. Create migration.
            if (in_array('migration', $creates)) {
                $migrationName = 'create_'.$table.'_table';

                $paths['migration'] = (new MigrationCreator(app('files')))->buildBluePrint(
                    $request->get('fields'),
                    $request->get('primary_key', 'id'),
                    $request->get('timestamps') == 1,
                    $request->get('soft_deletes') == 1
                )->create($migrationName, database_path('migrations'), $table);
            }

            if (in_array('lang', $creates)) {
                $paths['lang'] = (new LangCreator($request->get('fields')))
                    ->create($controller, $request->get('translate_title'));
            }

            if (in_array('repository', $creates)) {
                $paths['repository'] = (new RepositoryCreator())
                    ->create($model, $repository);
            }

            // Run migrate.
            if (in_array('migrate', $creates)) {
                Artisan::call('migrate');
                $message = Artisan::output();
            }

            // Make ide helper file.
            if (in_array('migrate', $creates) || in_array('controller', $creates)) {
                try {
                    Artisan::call('admin:ide-helper', ['-c' => $controller]);

                    $paths['ide-helper'] = 'dcat_admin_ide_helper.php';
                } catch (\Throwable $e) {
                }
            }
        } catch (\Exception $exception) {
            // Delete generated files if exception thrown.
            app('files')->delete($paths);

            return $this->backWithException($exception);
        }

        return $this->backWithSuccess($paths, $message);
    }

    /**
     * @return array
     */
    public function table()
    {
        $db = addslashes(\request('db'));
        $table = \request('tb');
        if (! $table || ! $db) {
            return ['status' => 1, 'list' => []];
        }

        $tables = collect($this->getDatabaseColumns($db, $table))
            ->filter(function ($v, $k) use ($db) {
                return $k == $db;
            })->map(function ($v) use ($table) {
                return Arr::get($v, $table);
            })
            ->filter()
            ->first();

        return ['status' => 1, 'list' => $tables];
    }

    /**
     * @return array
     */
    protected function getDatabaseColumns($db = null, $tb = null)
    {
        $databases = Arr::where(config('database.connections', []), function ($value) {
            $supports = ['mysql', 'pgsql'];

            return in_array(strtolower(Arr::get($value, 'driver')), $supports);
        });

        $data = [];

        try {
            foreach ($databases as $connectName => $value) {
                if ($db && $db != $value['database']) {
                    continue;
                }

                if (strtolower(Arr::get($value, 'driver')) === 'pgsql') {
                    continue;
                }

                $sql = sprintf('SELECT * FROM information_schema.columns WHERE table_schema = "%s"', $value['database']);

                if ($tb) {
                    $p = Arr::get($value, 'prefix');

                    $sql .= " AND TABLE_NAME = '{$p}{$tb}'";
                }

                $sql .= ' ORDER BY `ORDINAL_POSITION` ASC';

                $tmp = DB::connection($connectName)->select($sql);

                $collection = collect($tmp)->map(function ($v) use ($value) {
                    if (! $p = Arr::get($value, 'prefix')) {
                        return (array) $v;
                    }
                    $v = (array) $v;

                    $v['TABLE_NAME'] = Str::replaceFirst($p, '', $v['TABLE_NAME']);

                    return $v;
                });

                $data[$value['database']] = $collection->groupBy('TABLE_NAME')->map(function ($v) {
                    return collect($v)->keyBy('COLUMN_NAME')->map(function ($v) {
                        $v['COLUMN_TYPE'] = strtolower($v['COLUMN_TYPE']);
                        $v['DATA_TYPE'] = strtolower($v['DATA_TYPE']);

                        if (Str::contains($v['COLUMN_TYPE'], 'unsigned')) {
                            $v['DATA_TYPE'] .= '@unsigned';
                        }

                        return [
                            'type'     => $v['DATA_TYPE'],
                            'default'  => $v['COLUMN_DEFAULT'],
                            'nullable' => $v['IS_NULLABLE'],
                            'key'      => $v['COLUMN_KEY'],
                            'id'       => $v['COLUMN_KEY'] === 'PRI',
                            'comment'  => $v['COLUMN_COMMENT'],
                        ];
                    })->toArray();
                })->toArray();
            }
        } catch (\Throwable $e) {
        }

        // PostgreSQL connections are read separately, so that a failing connection
        // does not hide the tables of the other ones.
        foreach ($databases as $connectName => $value) {
            if (strtolower(Arr::get($value, 'driver')) !== 'pgsql' || ($db && $db != $value['database'])) {
                continue;
            }

            try {
                $data[$value['database']] = $this->getPgsqlColumns($connectName, $value, $tb);
            } catch (\Throwable $e) {
            }
        }

        return $data;
    }

    /**
     * Read the columns of a PostgreSQL connection, for every schema in its search path.
     * Tables outside the "public" schema are keyed as "schema.table".
     *
     * @param  string  $connectName
     * @param  array  $config
     * @param  string|null  $tb
     * @return array
     */
    protected function getPgsqlColumns($connectName, array $config, $tb = null)
    {
        $schemas = Arr::get($config, 'search_path', Arr::get($config, 'schema', 'public'));
        $schemas = is_array($schemas) ? $schemas : explode(',', (string) $schemas);
        $schemas = array_values(array_filter(array_map(function ($v) {
            return trim($v, " \t\n\r\0\x0B\"");
        }, $schemas))) ?: ['public'];

        $prefix = (string) Arr::get($config, 'prefix', '');
        $table = $tb;

        if ($tb && Str::contains($tb, '.')) {
            [$schema, $table] = explode('.', $tb, 2);
            $schemas = [$schema];
        }

        $sql = 'SELECT c.table_schema, c.table_name, c.column_name, c.data_type, c.udt_name, c.is_nullable, c.column_default, '
            .'pg_catalog.col_description((quote_ident(c.table_schema)||\'.\'||quote_ident(c.table_name))::regclass::oid, c.ordinal_position) AS column_comment, '
            .'CASE WHEN pk.column_name IS NULL THEN \'\' ELSE \'PRI\' END AS column_key '
            .'FROM information_schema.columns c '
            .'LEFT JOIN (SELECT ku.table_schema, ku.table_name, ku.column_name FROM information_schema.table_constraints tc '
            .'INNER JOIN information_schema.key_column_usage ku ON tc.constraint_name = ku.constraint_name AND tc.table_schema = ku.table_schema '
            .'WHERE tc.constraint_type = \'PRIMARY KEY\') pk '
            .'ON pk.table_schema = c.table_schema AND pk.table_name = c.table_name AND pk.column_name = c.column_name '
            .'WHERE c.table_schema IN ('.implode(',', array_fill(0, count($schemas), '?')).')';

        $bindings = $schemas;

        if ($table) {
            $sql .= ' AND c.table_name = ?';
            $bindings[] = $prefix.$table;
        }

        $sql .= ' ORDER BY c.table_schema, c.table_name, c.ordinal_position';

        $data = [];

        foreach (DB::connection($connectName)->select($sql, $bindings) as $v) {
            $v = (array) $v;

            $tableName = $prefix ? Str::replaceFirst($prefix, '', $v['table_name']) : $v['table_name'];

            if ($v['table_schema'] !== 'public') {
                $tableName = $v['table_schema'].'.'.$tableName;
            }

            $data[$tableName][$v['column_name']] = [
                'type'     => $this->convertPgsqlType($v['udt_name'] ?: $v['data_type']),
                'default'  => Str::startsWith((string) $v['column_default'], 'nextval(') ? null : $v['column_default'],
                'nullable' => $v['is_nullable'],
                'key'      => $v['column_key'],
                'id'       => $v['column_key'] === 'PRI',
                'comment'  => (string) $v['column_comment'],
            ];
        }

        return $data;
    }

    /**
     * Map a PostgreSQL type to the MySQL style type names used by the scaffold form.
     *
     * @param  string  $type
     * @return string
     */
    protected function convertPgsqlType($type)
    {
        $type = strtolower((string) $type);

        if (Str::startsWith($type, 'int') || in_array($type, ['serial', 'bigserial', 'smallserial'])) {
            return $type === 'int8' || $type === 'bigserial' ? 'bigint' : ($type === 'int2' ? 'smallint' : 'int');
        }

        $map = [
            'bool'        => 'tinyint',
            'float4'      => 'float',
            'float8'      => 'double',
            'numeric'     => 'decimal',
            'bpchar'      => 'char',
            'varchar'     => 'varchar',
            'text'        => 'text',
            'uuid'        => 'char',
            'json'        => 'json',
            'jsonb'       => 'json',
            'bytea'       => 'binary',
            'date'        => 'date',
            'time'        => 'time',
            'timetz'      => 'time',
            'timestamp'   => 'timestamp',
            'timestamptz' => 'timestamp',
        ];

        return $map[$type] ?? 'varchar';
    }

    protected function backWithException(\Exception $exception)
    {
        $error = new MessageBag([
            'title'   => 'Error',
            'message' => $exception->getMessage(),
        ]);

        return redirect()->refresh()->withInput()->with(compact('error'));
    }

    protected function backWithSuccess($paths, $message)
    {
        $messages = [];

        foreach ($paths as $name => $path) {
            $messages[] = ucfirst($name).": $path";
        }

        $messages[] = "<br />$message";

        $success = new MessageBag([
            'title'   => 'Success',
            'message' => implode('<br />', $messages),
        ]);

        return redirect()->refresh()->with(compact('success'));
    }
}
