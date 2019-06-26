<?php

namespace Dcat\Admin;

use Closure;
use Dcat\Admin\Exception\Handler;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Displayers;
use Dcat\Admin\Grid\Exporter;
use Dcat\Admin\Grid\Exporters\AbstractExporter;
use Dcat\Admin\Grid\Filter;
use Dcat\Admin\Grid\Header;
use Dcat\Admin\Grid\Model;
use Dcat\Admin\Grid\Responsive;
use Dcat\Admin\Grid\Row;
use Dcat\Admin\Grid\Tools;
use Dcat\Admin\Grid\Concerns;
use Dcat\Admin\Contracts\Repository;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\BuilderEvents;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Jenssegers\Mongodb\Eloquent\Model as MongodbModel;

class Grid
{
    use BuilderEvents,
        Concerns\HasElementNames,
        Concerns\Options,
        Concerns\MultipleHeader,
        Concerns\QuickSearch,
        Macroable {
            __call as macroCall;
        }

    /**
     * The grid data model instance.
     *
     * @var \Dcat\Admin\Grid\Model
     */
    protected $model;

    /**
     * Collection of all grid columns.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $columns;

    /**
     * Collection of all data rows.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $rows;

    /**
     * Rows callable fucntion.
     *
     * @var \Closure[]
     */
    protected $rowsCallback = [];

    /**
     * All column names of the grid.
     *
     * @var array
     */
    protected $columnNames = [];

    /**
     * Grid builder.
     *
     * @var \Closure
     */
    protected $builder;

    /**
     * Mark if the grid is built.
     *
     * @var bool
     */
    protected $built = false;

    /**
     * All variables in grid view.
     *
     * @var array
     */
    protected $variables = [];

    /**
     * The grid Filter.
     *
     * @var \Dcat\Admin\Grid\Filter
     */
    protected $filter;

    /**
     * Resource path of the grid.
     *
     * @var
     */
    protected $resourcePath;

    /**
     * Default primary key name.
     *
     * @var string
     */
    protected $keyName = 'id';

    /**
     * Export driver.
     *
     * @var string
     */
    protected $exporter;

    /**
     * View for grid to render.
     *
     * @var string
     */
    protected $view = 'admin::grid.table';

    /**
     * Per-page options.
     *
     * @var array
     */
    protected $perPages = [10, 20, 30, 50, 100, 200];

    /**
     * Default items count per-page.
     *
     * @var int
     */
    protected $perPage = 20;

    /**
     * Header tools.
     *
     * @var Tools
     */
    protected $tools;

    /**
     * Callback for grid actions.
     *
     * @var Closure[]
     */
    protected $actionsCallback = [];

    /**
     * @var Closure
     */
    protected $header;

    /**
     * @var Closure
     */
    protected $footer;

    /**
     * @var Closure
     */
    protected $wrapper;

    /**
     * @var Responsive
     */
    protected $responsive;

    /**
     * @var bool
     */
    protected $addNumberColumn = false;

    /**
     * Create a new grid instance.
     *
     * @param Repository $model
     * @param Closure  $builder
     */
    public function __construct(Repository $repository = null, $builder = null)
    {
        if ($repository) {
            $this->keyName = $repository->getKeyName();
        }
        $this->model   = new Model($repository);
        $this->columns = new Collection();
        $this->rows    = new Collection();
        $this->builder = $builder;

        $this->model()->setGrid($this);

        $this->setupTools();
        $this->setupFilter();

        $this->callResolving();
    }

    /**
     * Create a grid instance.
     *
     * @param mixed ...$params
     * @return $this
     */
    public static function make(...$params)
    {
        return new static(...$params);
    }

    /**
     * Enable responsive tables.
     * @see https://github.com/nadangergeo/RWD-Table-Patterns
     *
     * @return Responsive
     */
    public function responsive()
    {
        if (!$this->responsive) {
            $this->responsive = new Responsive($this);
        }

        return $this->responsive;
    }

    /**
     * @return bool
     */
    public function allowResponsive()
    {
        return $this->responsive ? true : false;
    }

    /**
     * Setup grid tools.
     */
    public function setupTools()
    {
        $this->tools = new Tools($this);
    }

    /**
     * Setup grid filter.
     *
     * @return void
     */
    protected function setupFilter()
    {
        $this->filter = new Filter($this->model());
    }

    /**
     * Handle export request.
     *
     * @param bool $forceExport
     */
    protected function handleExportRequest($forceExport = false)
    {
        if (!$scope = request(Exporter::$queryName)) {
            return;
        }

        // clear output buffer.
        if (ob_get_length()) {
            ob_end_clean();
        }

        $this->model()->usePaginate(false);

        if ($this->builder) {
            call_user_func($this->builder, $this);

            $this->getExporter($scope)->export();
        }

        if ($forceExport) {
            $this->getExporter($scope)->export();
        }
    }

    /**
     * @param string $scope
     *
     * @return AbstractExporter
     */
    protected function getExporter($scope)
    {
        return (new Exporter($this))->resolve($this->exporter)->withScope($scope);
    }

    /**
     * Get primary key name of model.
     *
     * @return string
     */
    public function getKeyName()
    {
        return $this->keyName ?: 'id';
    }

    /**
     * Set primary key name.
     *
     * @param string $name
     */
    public function setKeyName(string $name)
    {
        $this->keyName = $name;
    }

    /**
     * Add column to Grid.
     *
     * @param string $name
     * @param string $label
     *
     * @return Column|Collection
     */
    public function column($name, $label = '')
    {
        if (strpos($name, '.') !== false) {
            list($relationName, $relationColumn) = explode('.', $name);

            $label = empty($label) ? admin_trans_field($relationColumn) : $label;

            $name = Str::snake($relationName).'.'.$relationColumn;
        }

        $column = $this->addColumn($name, $label);

        return $column;
    }

    /**
     * Add number column.
     *
     * @param null|string $label
     * @return Column
     */
    public function number(?string $label = null)
    {
        return $this->column('#', $label ?: '#')->bold();
    }

    /**
     * Batch add column to grid.
     *
     * @example
     * 1.$grid->columns(['name' => 'Name', 'email' => 'Email' ...]);
     * 2.$grid->columns('name', 'email' ...)
     *
     * @param array $columns
     *
     * @return null
     */
    public function columns($columns = [])
    {
        if (func_num_args() == 0) {
            return $this->columns;
        }

        if (func_num_args() == 1 && is_array($columns)) {
            foreach ($columns as $column => $label) {
                $this->column($column, $label);
            }

            return;
        }

        foreach (func_get_args() as $column) {
            $this->column($column);
        }
    }

    /**
     * @return Collection
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Add column to grid.
     *
     * @param string $field
     * @param string $label
     *
     * @return Column
     */
    protected function addColumn($field = '', $label = '')
    {
        $column = new Column($field, $label);
        $column->setGrid($this);

        $this->columns->put($field, $column);

        return $column;
    }

    /**
     * Prepend column to grid.
     *
     * @param string $column
     * @param string $label
     *
     * @return Column
     */
    protected function prependColumn($column = '', $label = '')
    {
        $column = new Column($column, $label);
        $column->setGrid($this);

        $this->columns->prepend($column);

        return $column;
    }

    /**
     * Get Grid model.
     *
     * @return Model
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * @return array
     */
    public function getColumnNames()
    {
        return $this->columnNames;
    }

    /**
     * Paginate the grid.
     *
     * @param int $perPage
     *
     * @return void
     */
    public function paginate(int $perPage = 20)
    {
        $this->perPage = $perPage;

        $this->model()->setPerPage($perPage);
    }

    /**
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * Get the grid paginator.
     *
     * @return mixed
     */
    public function paginator()
    {
        if (!$this->options['show_pagination']) {
            return;
        }

        return new Tools\Paginator($this);
    }

    /**
     * If this grid use pagination.
     *
     * @return bool
     */
    public function allowPagination()
    {
        return $this->options['show_pagination'];
    }

    /**
     * Set per-page options.
     *
     * @param array $perPages
     */
    public function perPages(array $perPages)
    {
        $this->perPages = $perPages;

        return $this;
    }

    /**
     * Get per-page options.
     *
     * @return array
     */
    public function getPerPages()
    {
        return $this->perPages;
    }

    /**
     * Set grid action callback.
     *
     * @param Closure $callback
     *
     * @return $this
     */
    public function actions(Closure $callback)
    {
        $this->actionsCallback[] = $callback;

        return $this;
    }

    /**
     * Add `actions` column for grid.
     *
     * @return void
     */
    protected function appendActionsColumn()
    {
        if (!$this->options['show_actions']) {
            return;
        }

        $this->addColumn('__actions__', trans('admin.action'))
            ->displayUsing(Displayers\Actions::class, [$this->actionsCallback]);
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setRowSelectorOptions(array $options = [])
    {
        if (isset($options['style'])) {
            $this->options['row_selector_style'] = $options['style'];
        }
        if (isset($options['circle'])) {
            $this->options['row_selector_circle'] = $options['circle'];
        }
        if (isset($options['clicktr'])) {
            $this->options['row_selector_clicktr'] = $options['clicktr'];
        }
        if (isset($options['label_name'])) {
            $this->options['row_selector_label_name'] = $options['label_name'];
        }
        if (isset($options['bg'])) {
            $this->options['row_selector_bg'] = $options['bg'];
        }

        return $this;
    }

    /**
     * Prepend checkbox column for grid.
     *
     * @return void
     */
    protected function prependRowSelectorColumn()
    {
        if (!$this->options['show_row_selector']) {
            return;
        }

        $circle = $this->options['row_selector_circle'] ? 'checkbox-circle' : '';

        $column = new Column(
            Column::SELECT_COLUMN_NAME,
            <<<HTML
<div class="checkbox checkbox-{$this->options['row_selector_style']} $circle checkbox-grid">
    <input type="checkbox" class="select-all {$this->getSelectAllName()}"><label></label>
</div>
HTML
        );
        $column->setGrid($this);

        $column->displayUsing(Displayers\RowSelector::class);

        $this->columns->prepend($column, Column::SELECT_COLUMN_NAME);
    }

    /**
     * Build the grid.
     *
     * @return void
     */
    public function build()
    {
        if ($this->built) {
            return;
        }

        $this->applyQuickSearch();

        $collection = $this->processFilter(false);

        $data = $collection->toArray();

        $this->prependRowSelectorColumn();
        $this->appendActionsColumn();

        Column::setOriginalGridModels($collection);

        $this->columns->map(function (Column $column) use (&$data) {
            $column->fill($data);

            $this->columnNames[] = $column->getName();
        });

        $this->buildRows($data);

        $this->built = true;

        if ($data && $this->responsive) {
            $this->responsive->build();
        }

        $this->sortHeaders();
    }

    protected function createHeaderWithColumns(array $columns)
    {
        $headers = [];
        /* @var Column $column */
        foreach ($columns as $name => $column) {
            $header = new Header($this, $column->getLabel(), [$name]);
            $prio = $column->getDataPriority();
            if (is_int($prio)) {
                $header->responsive($prio);
            }
            if ($sorter = $column->sorter()) {
                $header->setSorter($sorter);
            }
            $headers[] = $header;
        }
        return $headers;
    }

    /**
     * @return Tools
     */
    public function getTools()
    {
        return $this->tools;
    }

    /**
     * Get filter of Grid.
     *
     * @return Filter
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Process the grid filter.
     *
     * @param bool $toArray
     *
     * @return array|Collection|mixed
     */
    public function processFilter($toArray = true)
    {
        if ($this->builder) {
            call_user_func($this->builder, $this);
        }

        return $this->filter->execute($toArray);
    }

    /**
     * Set the grid filter.
     *
     * @param Closure $callback
     * @return $this
     */
    public function filter(Closure $callback)
    {
        call_user_func($callback, $this->filter);

        return $this;
    }

    /**
     * Render the grid filter.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function renderFilter()
    {
        if (!$this->options['show_filter']) {
            return '';
        }

        return $this->filter->render();
    }

    /**
     * Expand filter.
     *
     * @return $this
     */
    public function expandFilter()
    {
        $this->filter->expand();

        return $this;
    }

    /**
     * Build the grid rows.
     *
     * @param array $data
     *
     * @return void
     */
    protected function buildRows(array $data)
    {
        $this->rows = collect($data)->map(function ($model) {
            return new Row($this, $model);
        });

        if ($this->rowsCallback) {
            foreach ($this->rowsCallback as $value) {
                $this->rows->map($value);
            }
        }
    }

    /**
     * Set grid row callback function.
     *
     * @param Closure $callable
     *
     * @return Collection|null
     */
    public function rows(Closure $callable = null)
    {
        if (is_null($callable)) {
            return $this->rows;
        }

        $this->rowsCallback[] = $callable;
    }

    /**
     * Setup grid tools.
     *
     * @param Closure $callback
     *
     * @return $this
     */
    public function tools(Closure $callback)
    {
        call_user_func($callback, $this->tools);

        return $this;
    }

    /**
     * Render custom tools.
     *
     * @return string
     */
    public function renderTools()
    {
        return $this->tools->render();
    }

    /**
     * Set exporter driver for Grid to export.
     *
     * @param $exporter
     *
     * @return $this
     */
    public function exporter($exporter)
    {
        $this->exporter = $exporter;

        return $this;
    }

    /**
     * Get the export url.
     *
     * @param int  $scope
     * @param null $args
     *
     * @return string
     */
    public function getExportUrl($scope = 1, $args = null)
    {
        $input = array_merge(Input::all(), Exporter::formatExportQuery($scope, $args));

        if ($constraints = $this->model()->getConstraints()) {
            $input = array_merge($input, $constraints);
        }

        return $this->getResource().'?'.http_build_query($input);
    }

    /**
     * Get create url.
     *
     * @return string
     */
    public function getCreateUrl()
    {
        $queryString = '';

        if ($constraints = $this->model()->getConstraints()) {
            $queryString = http_build_query($constraints);
        }

        return sprintf('%s/create%s',
            $this->getResource(),
            $queryString ? ('?'.$queryString) : ''
        );
    }

    /**
     * @param string $width
     * @param string $height
     * @return $this
     */
    public function setDialogFormDimensions(string $width, string $height)
    {
        $this->options['dialog_form_area'] = [$width, $height];

        return $this;
    }

    /**
     * If grid show export btn.
     *
     * @return bool
     */
    public function allowExportBtn()
    {
        return $this->options['show_exporter'];
    }

    /**
     * @param int|null $limit
     * @return Grid
     */
    public function setExportLimit(?int $limit)
    {
        return $this->option('export_limit', $limit);
    }

    /**
     * Render export button.
     *
     * @return string
     */
    public function renderExportButton()
    {
        if (!$this->options['show_exporter']) {
            return '';
        }
        return (new Tools\ExportButton($this))->render();
    }

    /**
     * If allow creation.
     *
     * @return bool
     */
    public function allowCreateBtn()
    {
        return $this->options['show_create_btn'];
    }

    /**
     * @return bool
     */
    public function allowQuickCreateBtn()
    {
        return $this->options['show_quick_create_btn'];
    }

    /**
     * Render create button for grid.
     *
     * @return string
     */
    public function renderCreateButton()
    {
        if (!$this->options['show_create_btn'] && !$this->options['show_quick_create_btn']) {
            return '';
        }

        return (new Tools\CreateButton($this))->render();
    }

    /**
     * @return $this
     */
    public function withBorder()
    {
        $this->options['show_bordered'] = true;

        return $this;
    }

    /**
     * Set grid header.
     *
     * @param Closure|string|Renderable $content
     *
     * @return $this|Closure
     */
    public function header($content = null)
    {
        if (!$content) {
            return $this->header;
        }
        $this->header = $content;
        return $this;
    }

    /**
     * Render grid header.
     *
     * @return string
     */
    public function renderHeader()
    {
        if (!$this->header) {
            return '';
        }

        $content = Helper::render($this->header, [$this->processFilter(false)]);

        if (empty($content)) {
            return '';
        }

        if ($content instanceof Renderable) {
            $content = $content->render();
        }

        if ($content instanceof Htmlable) {
            $content = $content->toHtml();
        }

        return <<<HTML
<div class="box-header clearfix" style="border-top:1px solid #ebeff2">{$content}</div>
HTML;
    }

    /**
     * Set grid footer.
     *
     * @param Closure|string|Renderable $content
     *
     * @return $this|Closure
     */
    public function footer($content = null)
    {
        if (!$content) {
            return $this->footer;
        }

        $this->footer = $content;

        return $this;
    }

    /**
     * Render grid footer.
     *
     * @return string
     */
    public function renderFooter()
    {
        if (!$this->footer) {
            return '';
        }

        $content = Helper::render($this->footer, [$this->processFilter(false)]);

        if (empty($content)) {
            return '';
        }

        if ($content instanceof Renderable) {
            $content = $content->render();
        }

        if ($content instanceof Htmlable) {
            $content = $content->toHtml();
        }

        return <<<HTML
    <div class="box-footer clearfix">{$content}</div>
HTML;
    }

    /**
     * Set resource path.
     *
     * @param string $path
     * @return $this
     */
    public function resource(string $path)
    {
        if (!empty($path)) {
            $this->resourcePath = URL::isValidUrl($path) ? $path : admin_base_path($path);
        }
        return $this;
    }

    /**
     * Get resource path.
     *
     * @return string
     */
    public function getResource()
    {
        return $this->resourcePath ?: app('request')->getPathInfo();
    }

    /**
     * @param Closure $closure
     * @return $this;
     */
    public function wrap(\Closure $closure)
    {
        $this->wrapper = $closure;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasWrapper()
    {
        return $this->wrapper ? true : false;
    }

    /**
     * Add variables to grid view.
     *
     * @param array $variables
     *
     * @return $this
     */
    public function with($variables = [])
    {
        $this->variables = $variables;

        return $this;
    }

    /**
     * Get all variables will used in grid view.
     *
     * @return array
     */
    protected function variables()
    {
        $this->variables['grid'] = $this;

        return $this->variables;
    }

    /**
     * Set a view to render.
     *
     * @param string $view
     * @param array  $variables
     */
    public function setView($view, $variables = [])
    {
        if (!empty($variables)) {
            $this->with($variables);
        }

        $this->view = $view;
    }

    /**
     * Set grid title.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->variables['title'] = $title;

        return $this;
    }

    /**
     * Set grid description.
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->variables['description'] = $description;

        return $this;
    }

    /**
     * Set resource path for grid.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setResource($path)
    {
        $this->resourcePath = $path;

        return $this;
    }

    /**
     * Get the string contents of the grid view.
     *
     * @return string
     */
    public function render()
    {
        $this->handleExportRequest(true);

        try {
            $this->callComposing();

            $this->build();
        } catch (\Throwable $e) {
            return Handler::renderException($e);
        }

        return $this->doWrap();
    }

    /**
     * @return string
     */
    protected function doWrap()
    {
        $view = view($this->view, $this->variables());

        if (!$wrapper = $this->wrapper) {
            return "<div class='box box-default'>{$view->render()}</div>";
        }

        return $wrapper($view);
    }

    /**
     * Add column to grid.
     *
     * @param string $name
     * @return Column|Collection
     */
    public function __get($name)
    {
        return $this->addColumn($name);
    }

    /**
     * Dynamically add columns to the grid view.
     *
     * @param $method
     * @param $arguments
     *
     * @return Column
     */
    public function __call($method, $arguments)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $arguments);
        }

        return $this->addColumn($method, $arguments[0] ?? null);
    }

}
