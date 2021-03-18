<?php

namespace Dcat\Admin\Grid\Concerns;

use Dcat\Admin\Contracts\Grid\ColumnSelectorStore;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Tools\ColumnSelector;
use Dcat\Admin\Support\Helper;
use Illuminate\Support\Collection;

trait CanHidesColumns
{
    /**
     * Default columns be hidden.
     *
     * @var array
     */
    public $hiddenColumns = [];

    /**
     * @var ColumnSelectorStore
     */
    private $columnSelectorStorage;

    /**
     * Remove column selector on grid.
     *
     * @param bool $disable
     *
     * @return $this|mixed
     */
    public function disableColumnSelector(bool $disable = true)
    {
        return $this->option('show_column_selector', ! $disable);
    }

    /**
     * @return bool
     */
    public function showColumnSelector(bool $show = true)
    {
        return $this->disableColumnSelector(! $show);
    }

    /**
     * @return bool
     */
    public function allowColumnSelector()
    {
        return $this->option('show_column_selector');
    }

    /**
     * @return string
     */
    public function renderColumnSelector()
    {
        if (! $this->allowColumnSelector()) {
            return '';
        }

        return (new ColumnSelector($this))->render();
    }

    /**
     * Setting default shown columns on grid.
     *
     * @param array|string $columns
     *
     * @return $this
     */
    public function hideColumns($columns)
    {
        if (func_num_args()) {
            $columns = (array) $columns;
        } else {
            $columns = func_get_args();
        }

        $this->hiddenColumns = array_merge($this->hiddenColumns, $columns);

        return $this;
    }

    /**
     * @return string
     */
    public function getColumnSelectorQueryName()
    {
        return $this->makeName(ColumnSelector::SELECT_COLUMN_NAME);
    }

    /**
     * Get visible columns from request query.
     *
     * @return array
     */
    public function getVisibleColumnsFromQuery()
    {
        if (! $this->allowColumnSelector()) {
            return [];
        }

        if (isset($this->visibleColumnsFromQuery)) {
            return $this->visibleColumnsFromQuery;
        }

        $columns = $input = Helper::array($this->request->get($this->getColumnSelectorQueryName()));

        if (! $input && ! $this->hasColumnSelectorRequestInput()) {
            $columns = $this->getVisibleColumnsFromStorage() ?: array_values(array_diff(
                $this->getComplexHeaderNames() ?: $this->columnNames, $this->hiddenColumns
            ));
        }

        $this->storeVisibleColumns($input);

        return $this->visibleColumnsFromQuery = $columns;
    }

    protected function formatWithComplexHeaders(array $columns)
    {
        if (! $columns) {
            return $this->getComplexHeaders();
        }

        if (empty($this->getComplexHeaderNames())) {
            return $columns;
        }

        return $this->getComplexHeaders()
            ->map(function (Grid\ComplexHeader $header) use ($columns) {
                if (! in_array($header->getName(), $columns, true)) {
                    return;
                }

                return $header->getColumnNames() ?: $this->getName();
            })
            ->filter()
            ->flatten()
            ->toArray();
    }

    /**
     * @return mixed
     */
    public function getVisibleComplexHeaders()
    {
        $visible = $this->getVisibleColumnsFromQuery();

        if (empty($visible)) {
            return $this->getComplexHeaders();
        }

        array_push($visible, Grid\Column::SELECT_COLUMN_NAME, Grid\Column::ACTION_COLUMN_NAME);

        return optional($this->getComplexHeaders())->filter(function ($column) use ($visible) {
            return in_array($column->getName(), $visible);
        });
    }

    /**
     * Get all visible column instances.
     *
     * @return Collection|static
     */
    public function getVisibleColumns()
    {
        if (! $this->allowColumnSelector()) {
            return $this->columns;
        }

        $visible = $this->formatWithComplexHeaders(
            $this->getVisibleColumnsFromQuery()
        );

        if (empty($visible)) {
            return $this->columns;
        }

        array_push($visible, Grid\Column::SELECT_COLUMN_NAME, Grid\Column::ACTION_COLUMN_NAME);

        return $this->columns->filter(function (Grid\Column $column) use ($visible) {
            return in_array($column->getName(), $visible);
        });
    }

    /**
     * Get all visible column names.
     *
     * @return array
     */
    public function getVisibleColumnNames()
    {
        if (! $this->allowColumnSelector()) {
            return $this->columnNames;
        }

        $visible = $this->formatWithComplexHeaders(
            $this->getVisibleColumnsFromQuery()
        );

        if (empty($visible)) {
            return $this->columnNames;
        }

        array_push($visible, Grid\Column::SELECT_COLUMN_NAME, Grid\Column::ACTION_COLUMN_NAME);

        return collect($this->columnNames)->filter(function ($column) use ($visible) {
            return in_array($column, $visible);
        })->toArray();
    }

    public function hasColumnSelectorRequestInput()
    {
        return $this->request->has($this->getColumnSelectorQueryName());
    }

    protected function storeVisibleColumns(array $input)
    {
        if (! $this->hasColumnSelectorRequestInput()) {
            return;
        }

        $this->getColumnSelectorStorage()->store($input);
    }

    protected function getVisibleColumnsFromStorage()
    {
        return $this->getColumnSelectorStorage()->get();
    }

    /**
     * @return ColumnSelectorStore
     */
    public function getColumnSelectorStorage()
    {
        return $this->columnSelectorStorage ?: ($this->columnSelectorStorage = $this->makeColumnSelectorStorage());
    }

    protected function makeColumnSelectorStorage()
    {
        $store = config('admin.grid.column_selector.store') ?: Grid\ColumnSelector\SessionStore::class;
        $params = (array) config('admin.grid.column_selector.store_params') ?: [];

        $storage = app($store, $params);

        $storage->setGrid($this);

        return $storage;
    }
}
