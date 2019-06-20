<?php

namespace Dcat\Admin\Grid\Exporters;

use Dcat\Admin\Grid;
use Illuminate\Support\Str;

abstract class AbstractExporter implements ExporterInterface
{
    /**
     * @var \Dcat\Admin\Grid
     */
    protected $grid;

    /**
     * @var \Closure
     */
    protected $builder;

    /**
     * @var array
     */
    public $titles;

    /**
     * @var array
     */
    public $data;

    /**
     * @var string
     */
    public $filename;

    /**
     * Create a new exporter instance.
     *
     * @param $builder
     */
    public function __construct($builder = null)
    {
        if ($builder instanceof \Closure) {
            $builder->bindTo($this);

            $this->builder = $builder;
        } elseif (is_array($builder)) {
            $this->titles = $builder;
        }
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

        return $this;
    }

    /**
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename ?? (date('Ymd-His') . '-' . Str::random(6));
    }

    /**
     * Get data with export query.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data ?? $this->grid->getFilter()->execute(true);
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
        $model = $this->grid->model();

        if ($scope == Grid\Exporter::SCOPE_ALL) {
            $model->usePaginate(true);
            $model->setPerPage($this->grid->option('export_limit'));
            $model->setCurrentPage(1);

            return $this;
        }

        list($scope, $args) = explode(':', $scope);

        if ($scope == Grid\Exporter::SCOPE_CURRENT_PAGE) {
            $model->usePaginate(true);
        }

        if ($scope == Grid\Exporter::SCOPE_SELECTED_ROWS) {
            $selected = explode(',', $args);
            $model->whereIn($this->grid->getKeyName(), $selected);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function export();

    /**
     * Create a new exporter instance.
     *
     * @param \Closure|array $closure
     */
    public static function create($builder = null)
    {
        return new static($builder);
    }
}
