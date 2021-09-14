<?php

namespace Dcat\Admin\Grid\Concerns;

use Closure;
use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Dcat\Admin\Support\Helper;
use Illuminate\Support\Collection;

trait HasFilter
{
    /**
     * The grid Filter.
     *
     * @var Grid\Filter
     */
    protected $filter;

    /**
     * Setup grid filter.
     *
     * @return void
     */
    protected function setUpFilter()
    {
        $this->filter = new Grid\Filter($this->model());
    }

    /**
     * Process the grid filter.
     *
     * @param  bool  $toArray
     * @return Collection
     */
    public function processFilter()
    {
        $this->callBuilder();
        $this->handleExportRequest();

        $this->applyQuickSearch();
        $this->applyColumnFilter();
        $this->applySelectorQuery();

        return $this->filter->execute();
    }

    /**
     * Get or set the grid filter.
     *
     * @param  Closure  $callback
     * @return $this|Grid\Filter
     */
    public function filter(Closure $callback = null)
    {
        if ($callback === null) {
            return $this->filter;
        }

        call_user_func($callback, $this->filter);

        return $this;
    }

    /**
     * Render the grid filter.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function renderFilter()
    {
        if (! $this->options['filter']) {
            return '';
        }

        return $this->filter->render();
    }

    /**
     * Expand filter.
     *
     * @return $this
     */
    public function expandFilter()
    {
        $this->filter->expand();

        return $this;
    }

    /**
     * Disable grid filter.
     *
     * @return $this
     */
    public function disableFilter(bool $disable = true)
    {
        $this->filter->disableCollapse($disable);

        return $this->option('filter', ! $disable);
    }

    /**
     * Show grid filter.
     *
     * @param  bool  $val
     * @return $this
     */
    public function showFilter(bool $val = true)
    {
        return $this->disableFilter(! $val);
    }

    /**
     * Disable filter button.
     *
     * @param  bool  $disable
     * @return $this
     */
    public function disableFilterButton(bool $disable = true)
    {
        $this->tools->disableFilterButton($disable);

        return $this;
    }

    /**
     * Show filter button.
     *
     * @param  bool  $val
     * @return $this
     */
    public function showFilterButton(bool $val = true)
    {
        return $this->disableFilterButton(! $val);
    }

    protected function addFilterScript()
    {
        if (! $this->isAsyncRequest()) {
            return;
        }

        Admin::script(
            <<<JS
var count = {$this->filter()->countConditions()};

$('.async-{$this->getTableId()}').find('.filter-count').text(count > 0 ? ('('+count+')') : '');
JS
        );

        $url = Helper::urlWithoutQuery($this->filter()->urlWithoutFilters(), ['_pjax', static::ASYNC_NAME]);

        Admin::script("$('.grid-filter-form').attr('action', '{$url}');", true);
    }
}
