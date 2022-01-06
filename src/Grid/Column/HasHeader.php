<?php

namespace Dcat\Admin\Grid\Column;

use Dcat\Admin\Exception\RuntimeException;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Model;
use Dcat\Admin\Support\Helper;
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
     * @param  string|Renderable|Htmlable  $header
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
     * @param  string  $columnName
     * @param  string  $cast
     * @return $this
     */
    public function sortable($columnName = null, $cast = null)
    {
        $sorter = new Sorter($this->grid, $columnName ?: $this->getName(), $cast);

        return $this->addHeader($sorter);
    }

    /**
     * Set column filter.
     *
     * @example
     *      $grid->username()->filter();
     *
     *      $grid->user()->filter('user.id');
     *
     *      $grid->user()->filter(function () {
     *          return $this->user['id'];
     *      });
     *
     *      $grid->username()->filter(
     *          Grid\Column\Filter\StartWith::make(__('admin.username'))
     *      );
     *
     *      $grid->created_at()->filter(
     *          Grid\Column\Filter\Equal::make(__('admin.created_at'))->date()
     *      );
     *
     * @param  Grid\Column\Filter|string  $filter
     * @return $this
     */
    public function filter($filter = null)
    {
        $valueKey = is_string($filter) || $filter instanceof \Closure ? $filter : null;

        if (! $filter || $valueKey) {
            $filter = Grid\Column\Filter\Equal::make()->valueFilter($valueKey);
        }

        if (! $filter instanceof Grid\Column\Filter) {
            throw new RuntimeException('The "$filter" must be a type of '.Grid\Column\Filter::class.'.');
        }

        return $this->addHeader($filter);
    }

    /**
     * @param  string|\Closure  $valueKey
     * @return $this
     */
    public function filterByValue($valueKey = null)
    {
        return $this->filter(
            Grid\Column\Filter\Equal::make()
                ->valueFilter($valueKey)
                ->hide()
        );
    }

    /**
     * Add a help tooltip to column header.
     *
     * @param  string|\Closure  $message
     * @param  null|string  $style  'green', 'blue', 'red', 'purple'
     * @param  null|string  $placement  'bottom', 'left', 'right', 'top'
     * @return $this
     */
    public function help($message, ?string $style = null, ?string $placement = null)
    {
        return $this->addHeader(new Help($message, $style, $placement));
    }

    /**
     * Add a binding based on filter to the model query.
     *
     * @param  Model  $model
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
        if (! $this->headers) {
            return '';
        }
        $headers = implode(
            '',
            array_map(
                [Helper::class, 'render'],
                $this->headers
            )
        );

        return "<span class='grid-column-header'>$headers</span>";
    }
}
