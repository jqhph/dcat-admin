<?php

namespace Dcat\Admin\Grid\Column;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Grid\Model;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;

/**
 * @property Grid $grid
 */
trait HasHeader
{
    /**
     * @var Filter
     */
    public $filter;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * Add contents to column header.
     *
     * @param string|Renderable|Htmlable $header
     *
     * @return $this
     */
    public function addHeader($header)
    {
        if ($header instanceof Filter) {
            $header->setParent($this);
            $this->filter = $header;
        }

        $this->headers[] = $header;

        return $this;
    }

    /**
     * Add a column sortable to column header.
     *
     * @param string $cast
     *
     * @return $this
     */
    protected function addSorter($cast = null)
    {
        $sortName = $this->grid->model()->getSortName();

        $sorter = new Sorter($sortName, $this->getName(), $cast);

        return $this->addHeader($sorter);
    }

    /**
     * Add a help tooltip to column header.
     *
     * @param string|\Closure $message
     * @param null|string     $style     'green', 'blue', 'red', 'purple'
     * @param null|string     $placement 'bottom', 'left', 'right', 'top'
     *
     * @return $this
     */
    protected function addHelp($message, ?string $style = null, ?string $placement = 'bottom')
    {
        return $this->addHeader(new Help($message, $style, $placement));
    }

    /**
     * Add a filter to column header.
     *
     * @param \Closure $builder
     *
     * @return $this
     */
    protected function addFilter(Filter $filter)
    {
        return $this->addHeader($filter);
    }

    /**
     * Add a binding based on filter to the model query.
     *
     * @param Model $model
     */
    public function bindFilterQuery(Model $model)
    {
        if ($this->filter) {
            $this->filter->addBinding($this->filter->value(), $model);
        }
    }

    /**
     * Render Column header.
     *
     * @return string
     */
    public function renderHeader()
    {
        $headers = collect($this->headers)->map(function ($item) {
            if ($item instanceof Renderable) {
                return $item->render();
            }

            if ($item instanceof Htmlable) {
                return $item->toHtml();
            }

            return (string) $item;
        })->implode('');

        return "<span class='grid-column-header'>$headers</span>";
    }
}
