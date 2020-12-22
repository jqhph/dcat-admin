<?php

namespace Dcat\Admin\Console;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Class IdeHelperCommand.
 *
 * @authr jqh <841324345@qq.com>
 */
class IdeHelperCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:ide-helper  {--c|controller= : Controller class. } ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the ide-helper file';

    /**
     * @var array
     */
    protected $patterns = [
        'grid'        => '/(?:grid)->([\w0-9_]+)(?:\(|;|->|\s)/i',
        'show'        => '/show->([\w0-9_]+)(?:\(|;|->|\s)/i',
        'grid-column' => '/@method[\s]+\$this[\s]+([\w0-9_]+)/i',
        'form-field'  => '/@method[\s]+[\\\\\w0-9_]+[\s]+([\w0-9_]+)/i',
        'grid-filter' => '/@method[\s]+[\\\\\w0-9_]+[\s]+([\w0-9_]+)/i',
    ];

    /**
     * @var array
     */
    protected $templates = [
        'grid' => [
            'method'   => '* @method Grid\Column|Collection %s(string $label = null)',
            'property' => '* @property Grid\Column|Collection %s',
        ],
        'show' => [
            'method'   => '* @method Show\Field|Collection %s(string $label = null)',
            'property' => '* @property Show\Field|Collection %s',
        ],
        'form'        => '* @method %s %s(...$params)',
        'grid-column' => '* @method $this %s(...$params)',
        'grid-filter' => '* @method %s %s(...$params)',
        'show-column' => '* @method $this %s(...$params)',
    ];

    protected $path = 'dcat_admin_ide_helper.php';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! config('app.debug')) {
            $this->error('Permission deny!');

            return;
        }
        if (is_file($bootstrap = admin_path('bootstrap.php'))) {
            require $bootstrap;
        }

        $builders = $this->getBuilderMethods();

        // Get all fields.
        $fields = $this->getFieldsFromControllerFiles($builders)
            ->merge($this->getFieldsFromDatabase($builders))
            ->unique();

        $this->write($fields);

        $path = basename($this->path);

        $this->info("The helper file [$path] created successfully.");
    }

    /**
     * @param array $reject
     *
     * @return Collection
     */
    protected function getFieldsFromDatabase(array $reject = [])
    {
        $databases = Arr::where(config('database.connections', []), function ($value) {
            $supports = ['mysql'];

            return in_array(strtolower(Arr::get($value, 'driver')), $supports);
        });

        $exceptTables = [
            'migrations',
            'phinxlog',
        ];

        $data = collect();

        try {
            foreach ($databases as $connectName => $value) {
                $sql = sprintf('SELECT * FROM information_schema.columns WHERE table_schema = "%s"', $value['database']);

                $each = collect(DB::connection($connectName)->select($sql))
                    ->map(function ($v) use ($exceptTables, &$reject) {
                        $v = (array) $v;

                        if (in_array($v['TABLE_NAME'], $exceptTables) || in_array($v['COLUMN_NAME'], $reject)) {
                            return;
                        }

                        return $v['COLUMN_NAME'];
                    })
                    ->filter();

                $data = $data->merge($each);
            }
        } catch (\Throwable $e) {
        }

        return $data->unique();
    }

    /**
     * @param array $reject
     *
     * @return Collection
     */
    protected function getFieldsFromControllerFiles(array $reject = [])
    {
        $option = $this->option('controller');

        return $this->getAllControllers()
            ->merge(explode(',', $option))
            ->map(function ($controller) use (&$reject) {
                if (! $controller || ! $content = $this->getClassContent($controller)) {
                    return [];
                }

                preg_match_all($this->patterns['grid'], $content, $grid);
                preg_match_all($this->patterns['show'], $content, $show);

                $grid = $grid[1];
                $show = $show[1];

                return collect(array_merge($grid, $show))->reject(function ($name) use (&$reject) {
                    return in_array($name, $reject);
                });
            })
            ->flatten()
            ->unique()
            ->filter();
    }

    /**
     * @param Collection $fields
     */
    protected function write(Collection $fields)
    {
        $content = str_replace(
            [
                '{grid}',
                '{show}',
                '{form}',
                '{grid-column}',
                '{grid-filter}',
                '{show-column}',
            ],
            [
                $this->generate('grid', $fields),
                $this->generate('show', $fields),
                $this->generateFormFields(),
                $this->generateGridColumns(),
                $this->generateGridFilters(),
                $this->generateShowFields(),
            ],
            File::get($this->getStub())
        );

        File::put(base_path($this->path), $content);
    }

    /**
     * @param string     $type
     * @param Collection $fields
     *
     * @return string
     */
    public function generate(string $type, Collection $fields)
    {
        $methods = $properties = [];
        $space = str_repeat(' ', 5);

        $fields->each(function ($name) use ($type, &$methods, &$properties, $space) {
            $properties[] = $space.sprintf($this->templates[$type]['property'], $name);
            $methods[] = $space.sprintf($this->templates[$type]['method'], $name);
        });

        return trim(implode("\r\n", array_merge($properties, [$space.'*'], $methods)));
    }

    /**
     * @return string
     */
    protected function generateGridFilters()
    {
        $content = $this->getClassContent(Grid\Filter::class);

        preg_match_all($this->patterns['grid-filter'], $content, $fields);

        $reject = $fields[1];

        $fields = collect(Grid\Filter::extensions())->reject(function ($value, $key) use (&$reject) {
            return in_array($key, $reject);
        });

        $space = str_repeat(' ', 5);

        return trim(
            $fields
                ->map(function ($value, $key) use (&$space) {
                    return $space.sprintf($this->templates['grid-filter'], '\\'.$value, $key);
                })
                ->implode("\r\n")
        );
    }

    /**
     * @return string
     */
    protected function generateShowFields()
    {
        $extensions = collect(Show\Field::extensions());

        $space = str_repeat(' ', 5);

        return trim(
            $extensions
                ->map(function ($value, $key) use (&$space) {
                    return $space.sprintf($this->templates['show-column'], $key);
                })
                ->implode("\r\n")
        );
    }

    /**
     * @return string
     */
    protected function generateFormFields()
    {
        $content = $this->getClassContent(Form::class);

        preg_match_all($this->patterns['form-field'], $content, $fields);

        $reject = $fields[1];

        $fields = collect(Form::extensions())->reject(function ($value, $key) use (&$reject) {
            return in_array($key, $reject);
        });

        $space = str_repeat(' ', 5);

        return trim(
            $fields
                ->map(function ($value, $key) use (&$space) {
                    return $space.sprintf($this->templates['form'], '\\'.$value, $key);
                })
                ->implode("\r\n")
        );
    }

    /**
     * @return string
     */
    protected function generateGridColumns()
    {
        $content = $this->getClassContent(Grid\Column::class);

        preg_match_all($this->patterns['grid-column'], $content, $column);

        $reject = $column[1];

        $columns = collect(array_keys(Grid\Column::extensions()))->reject(function ($displayer) use (&$reject) {
            return in_array($displayer, $reject);
        });

        $space = str_repeat(' ', 5);

        return trim(
            $columns
                ->map(function ($value) use (&$space) {
                    return $space.sprintf($this->templates['grid-column'], $value);
                })
                ->implode("\r\n")
        );
    }

    /**
     * @return array
     */
    protected function getBuilderMethods()
    {
        $grid = new \ReflectionClass(Grid::class);

        $grids = collect($grid->getMethods())
            ->pluck('name')
            ->merge(collect($grid->getProperties())->pluck('name'))
            ->all();

        $show = new \ReflectionClass(Show::class);

        return collect($show->getMethods())
            ->pluck('name')
            ->merge(collect($show->getProperties())->pluck('name'))
            ->merge($grids)
            ->unique()
            ->all();
    }

    /**
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/ide-helper.stub';
    }

    /**
     * Get all registered controllers.
     *
     * @return Collection
     */
    public function getAllControllers()
    {
        return collect(\Route::getRoutes())->map(function ($route) {
            try {
                $action = $route->getActionName();

                if ($action == 'Closure') {
                    return;
                }

                return explode('@', $action)[0];
            } catch (\Exception $e) {
            }
        })->filter();
    }

    public function getClassContent($class)
    {
        if ($file = $this->getFileNameByClass($class)) {
            return File::get($file);
        }
    }

    /**
     * @param string $class
     *
     * @return string
     */
    public function getFileNameByClass($class)
    {
        if (! class_exists($class)) {
            return;
        }

        return (new \ReflectionClass($class))->getFileName();
    }
}
