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
use Illuminate\Support\Facades\Schema;
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
        $tables = $this->getDatabaseTables();

        return $content
            ->title(trans('admin.scaffold.header'))
            ->description(' ')
            ->body(view(
                'admin::helpers.scaffold',
                compact('dbTypes', 'action', 'tables', 'dataTypeMap')
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
        if (!$table || !$db) {
            return ['status' => 1, 'list' => []];
        }
        $tables = $this->getTableColumns($table);

        return ['status' => 1, 'list' => $tables];
    }

    /**
     * @return array
     */
    protected function getDatabaseTables()
    {
        $data = [];
        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $tables=$sm->listTables();
        $db=env('DB_DATABASE');
        foreach ($tables as $table) {
            $data[$db][] = $table->getName();
        }
        return $data;
    }

    /**
     * @return array
     */
    protected function getTableColumns($tb = null)
    {
        $data = [];
        if (!$tb) {
            return $data;
        }

        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $columns = $sm->listTableColumns($tb);
        foreach ($columns as $c) {
            $type = $c->getType()->getName();
            if ($c->getUnsigned()) {
                $type .= '@unsigned';
            }
            $data[$c->getName()] = [
                'type' => $type,
                'comment' => $c->getComment(),
                'id' => false,
                "default" => $c->getDefault(),
                "nullable" => $c->getNotnull(),
                "key"=>''
            ];
        }

        $indexes = $sm->listTableIndexes($tb);
        foreach ($indexes as $index) {
            $index_columns=$index->getColumns();
            if (count($index_columns)==1){
                if ($index->isSimpleIndex()){
                    $data[$index_columns[0]]['key']='index';
                }
                else{
                    if ($index->isPrimary()){
                        $data[$index_columns[0]]['id']=$index->isPrimary();
                        $data[$index_columns[0]]['key']='primary';
                    }
                    else
                    {
                        $data[$index_columns[0]]['key']='unique';
                    }
                }
            }
        }

        return $data;
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
