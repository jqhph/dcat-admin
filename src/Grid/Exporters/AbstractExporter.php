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
    public $titles = [];

    /**
     * @var array
     */
    public $data;

    /**
     * @var string
     */
    public $filename;

    /**
     * @var string
     */
    protected $scope;

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
     * @param int $page
     * @param int $perPage
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
            $page    = $model->getCurrentPage();
            $perPage = $model->getPerPage();
        }

        $model->reset();
        $model->usePaginate(false);

        if ($page && $this->scope !== Grid\Exporter::SCOPE_SELECTED_ROWS) {
            $perPage = $perPage ?: $this->grid->option('export_chunk_size');

            $model->forPage($page, $perPage);
        }

        return $this->grid->getFilter()->execute(true);
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
        $args  = $data[1] ?? '';

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
     * Create a new exporter instance.
     *
     * @param \Closure|array $closure
     */
    public static function create($builder = null)
    {
        return new static($builder);
    }
}
