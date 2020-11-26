<?php

namespace Dcat\Admin\Grid\Column;

use Illuminate\Contracts\Support\Renderable;

class Sorter implements Renderable
{
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
    protected $sortName;

    /**
     * @var string
     */
    protected $columnName;

    /**
     * Sorter constructor.
     *
     * @param string $sortName
     * @param string $columnName
     * @param string $cast
     */
    public function __construct($sortName, $columnName, $cast)
    {
        $this->sortName = $sortName;
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
        $this->sort = app('request')->get($this->sortName);

        if (empty($this->sort)) {
            return false;
        }

        return isset($this->sort['column']) && $this->sort['column'] == $this->columnName;
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
                $this->sortName => $sort,
            ]);
        } else {
            $url = request()->fullUrlWithQuery([
                $this->sortName => [],
            ]);
        }

        return "&nbsp;<a href='{$url}' class='grid-sort feather icon-arrow-{$icon} {$active}'></a>";
    }
}
