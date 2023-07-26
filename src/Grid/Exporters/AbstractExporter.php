<?php

namespace Dcat\Admin\Grid\Exporters;

use Dcat\Admin\Grid;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @method $this disableExportAll(bool $value = true)
 * @method $this disableExportCurrentPage(bool $value = true)
 * @method $this disableExportSelectedRow(bool $value = true)
 */
abstract class AbstractExporter implements ExporterInterface
{
    /**
     * @var \Dcat\Admin\Grid
     */
    protected $grid;

    /**
     * @var Grid\Exporter
     */
    protected $parent;

    /**
     * @var \Closure
     */
    protected $builder;

    /**
     * @var array
     */
    protected $titles = [];

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var string
     */
    protected $scope;

    /**
     * @var string
     */
    protected $extension = 'xlsx';

    /**
     * Create a new exporter instance.
     *
     * @param  array  $titles
     */
    public function __construct($titles = [])
    {
        if ($titles) {
            $this->titles($titles);
        }
    }

    /**
     * Set the headings of excel sheet.
     *
     * @param  array|false  $titles
     * @return $this|array
     */
    public function titles($titles = null)
    {
        if ($titles === null) {
            return $this->titles ?: ($this->titles = $this->defaultTitles());
        }

        if (is_array($titles) || $titles === false) {
            $this->titles = $titles;
        }

        return $this;
    }

    /**
     * 读取默认标题.
     *
     * @return array
     */
    protected function defaultTitles()
    {
        return $this
            ->grid
            ->columns()
            ->mapWithKeys(function (Grid\Column $column, $name) {
                return [$name => $column->getLabel()];
            })
            ->reject(function ($v, $k) {
                return in_array($k, ['#', Grid\Column::ACTION_COLUMN_NAME, Grid\Column::SELECT_COLUMN_NAME]);
            })
            ->toArray();
    }

    /**
     * Set filename.
     *
     * @param  string|\Closure  $filename
     * @return $this
     */
    public function filename($filename)
    {
        $this->filename = value($filename);

        return $this;
    }

    /**
     * Set export data callback function.
     *
     * @param  \Closure  $builder
     * @return $this
     */
    public function rows(\Closure $builder)
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * @return $this
     */
    public function xlsx()
    {
        return $this->extension('xlsx');
    }

    /**
     * @return $this
     */
    public function csv()
    {
        return $this->extension('csv');
    }

    /**
     * @return $this
     */
    public function ods()
    {
        return $this->extension('ods');
    }

    /**
     * @param  string  $ext  e.g. csv/xlsx/ods
     * @return $this
     */
    public function extension(string $ext)
    {
        $this->extension = $ext;

        return $this;
    }

    /**
     * Set grid for exporter.
     *
     * @param  Grid  $grid
     * @return $this
     */
    public function setGrid(Grid $grid)
    {
        $this->grid = $grid;
        $this->parent = $grid->exporter();

        return $this;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename ?: (admin_trans_label().'-'.date('Ymd-His').'-'.Str::random(6));
    }

    /**
     * Get data with export query.
     *
     * @param  int  $page
     * @param  int  $perPage
     * @return array|\Illuminate\Support\Collection|mixed
     */
    public function buildData(?int $page = null, ?int $perPage = null)
    {
        $model = $this->getGridModel();

        // current page
        if ($this->scope === Grid\Exporter::SCOPE_CURRENT_PAGE) {
            $page = $model->getCurrentPage();
            $perPage = $model->getPerPage();
        }

        $model->usePaginate(false);

        if ($page && $this->scope !== Grid\Exporter::SCOPE_SELECTED_ROWS) {
            $perPage = $perPage ?: $this->getChunkSize();

            $model->forPage($page, $perPage);
        }

        $array = $this->grid->processFilter();

        $model->reset();

        return $this->normalize($this->callBuilder($array));
    }

    /**
     * 格式化待导出数据.
     *
     * @param  Collection  $data
     * @return array
     */
    protected function normalize(Collection $data)
    {
        $data = $data->toArray();
        foreach ($data as &$row) {
            $row = Arr::dot($row);

            foreach ($row as &$v) {
                if (is_array($v) || is_object($v)) {
                    $v = json_encode($v, JSON_UNESCAPED_UNICODE);
                }
            }
        }

        return $data;
    }

    /**
     * @return Grid\Model
     */
    protected function getGridModel()
    {
        $model = $this->grid->model();

        if (empty($this->modelQueries)) {
            $model->rejectQuery(['forPage']);

            $this->modelQueries = clone $model->getQueries();
        }

        $model->setQueries($this->modelQueries);

        return $model;
    }

    /**
     * @param  Collection  $data
     * @return array
     */
    protected function callBuilder(Collection &$data)
    {
        if ($data && $this->builder) {
            return ($this->builder)($data);
        }

        return $data;
    }

    /**
     * @return int
     */
    protected function getChunkSize()
    {
        return $this->parent->option('chunk_size') ?: 5000;
    }

    /**
     * Export data with scope.
     *
     * @param  string  $scope
     * @return $this
     */
    public function withScope($scope)
    {
        $data = explode(':', $scope);
        $scope = $data[0] ?? '';
        $args = $data[1] ?? '';

        $this->scope = $scope;

        if ($scope == Grid\Exporter::SCOPE_SELECTED_ROWS) {
            $selected = explode(',', $args);

            $this->grid->model()->whereIn($this->grid->getKeyName(), $selected);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function export();

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $this->parent->{$method}(...$arguments);

        return $this;
    }

    /**
     * Create a new exporter instance.
     *
     * @param  \Closure|array  $closure
     */
    public static function make($builder = null)
    {
        return new static($builder);
    }
}
