<?php

namespace Dcat\Admin\Grid\Exporters;

use Dcat\Admin\Grid;
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
     * @var array
     */
    protected $data;

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
     * @param array $titles
     */
    public function __construct($titles = [])
    {
        $this->titles($titles);
    }

    /**
     * Set the headings of excel sheet.
     *
     * @param array|false $titles
     *
     * @return $this
     */
    public function titles($titles)
    {
        if (is_array($titles) || $titles === false) {
            $this->titles = $titles;
        }

        return $this;
    }

    /**
     * Set filename.
     *
     * @param string|\Closure $filename
     *
     * @return $this
     */
    public function filename($filename)
    {
        $this->filename = value($filename);

        return $this;
    }

    /**
     * Set export data.
     *
     * @param array $data
     *
     * @return $this
     */
    public function data($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set export data callback function.
     *
     * @param \Closure $builder
     *
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
     * @param string $ext e.g. csv/xlsx/ods
     *
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
     * @param Grid $grid
     *
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
     * @param int $page
     * @param int $perPage
     *
     * @return array|\Illuminate\Support\Collection|mixed
     */
    public function buildData(?int $page = null, ?int $perPage = null)
    {
        if (! is_null($this->data)) {
            return $this->data;
        }

        $model = $this->grid->model();

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

        $array = $this->grid->processFilter(true);

        $model->reset();
        $model->rejectQueries('forPage');

        if ($this->builder) {
            return ($this->builder)($array);
        }

        return $array;
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
     * @param string $scope
     *
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

            $this->grid->model()->whereIn($this->grid->keyName(), $selected);
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
     *
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
     * @param \Closure|array $closure
     */
    public static function make($builder = null)
    {
        return new static($builder);
    }
}
