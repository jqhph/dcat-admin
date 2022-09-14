<?php

namespace Dcat\Admin\Grid\Filter\Layout;

use Dcat\Admin\Grid\Filter\AbstractFilter;
use Illuminate\Support\Collection;

class Column
{
    /**
     * @var Collection
     */
    protected $filters;

    /**
     * @var int
     */
    protected $width;

    /**
     * Column constructor.
     *
     * @param  int  $width
     */
    public function __construct($width = 12)
    {
        $this->width = $width;
        $this->filters = new Collection();
    }

    /**
     * Add a filter to this column.
     *
     * @param  AbstractFilter  $filter
     */
    public function addFilter(AbstractFilter $filter)
    {
        $this->filters->push($filter);
    }

    /**
     * Get all filters in this column.
     *
     * @return Collection
     */
    public function filters()
    {
        return $this->filters;
    }

    /**
     * Get or set column width.
     *
     * @return int|void
     */
    public function width($width = null)
    {
        if ($width === null) {
            return $this->width;
        }

        $this->width = $width;
    }

    /**
     * Remove filter from column by id.
     */
    public function removeFilterByID($id)
    {
        $this->filters = $this->filters->reject(function (AbstractFilter $filter) use ($id) {
            return $filter->getId() == $id;
        });
    }
}
