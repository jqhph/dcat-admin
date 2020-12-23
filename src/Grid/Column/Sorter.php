<?php

namespace Dcat\Admin\Grid\Column;

use Dcat\Admin\Grid;
use Illuminate\Contracts\Support\Renderable;

class Sorter implements Renderable
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * Sort arguments.
     *
     * @var array
     */
    protected $sort;

    /**
     * Cast Name.
     *
     * @var array
     */
    protected $cast;

    /**
     * @var string
     */
    protected $columnName;

    /**
     * Sorter constructor.
     *
     * @param Grid $grid
     * @param string $columnName
     * @param string $cast
     */
    public function __construct(Grid $grid, $columnName, $cast)
    {
        $this->grid = $grid;
        $this->columnName = $columnName;
        $this->cast = $cast;
    }

    /**
     * Determine if this column is currently sorted.
     *
     * @return bool
     */
    protected function isSorted()
    {
        $this->sort = app('request')->get($this->getSortName());

        if (empty($this->sort)) {
            return false;
        }

        return isset($this->sort['column']) && $this->sort['column'] == $this->columnName;
    }

    protected function getSortName()
    {
        return $this->grid->model()->getSortName();
    }

    /**
     * @return string
     */
    public function render()
    {
        $type = 'desc';
        $icon = 'up';
        $active = '';

        if ($this->isSorted()) {
            $type = $this->sort['type'] == 'desc' ? 'asc' : 'desc';
            $active = 'active';

            if ($this->sort['type'] === 'asc') {
                $icon = 'down';
            }
        }

        $sort = ['column' => $this->columnName, 'type' => $type];

        if ($this->cast) {
            $sort['cast'] = $this->cast;
        }

        if (! $this->isSorted() || $this->sort['type'] != 'asc') {
            $url = request()->fullUrlWithQuery([
                $this->getSortName() => $sort,
            ]);
        } else {
            $url = request()->fullUrlWithQuery([
                $this->getSortName() => [],
            ]);
        }

        return "&nbsp;<a href='{$url}' class='grid-sort feather icon-arrow-{$icon} {$active}'></a>";
    }
}
