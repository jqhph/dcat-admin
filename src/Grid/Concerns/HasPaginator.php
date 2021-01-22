<?php

namespace Dcat\Admin\Grid\Concerns;

use Dcat\Admin\Grid\Tools;

trait HasPaginator
{
    /**
     * @var Tools\Paginator
     */
    protected $paginator;

    /**
     * Per-page options.
     *
     * @var array
     */
    protected $perPages = [10, 20, 30, 50, 100, 200];

    /**
     * Default items count per-page.
     *
     * @var int
     */
    protected $perPage = 20;

    /**
     * @var string
     */
    protected $paginatorClass = Tools\Paginator::class;

    /**
     * @var \Closure
     */
    protected $paginationCallback;

    /**
     * Paginate the grid.
     *
     * @param int $perPage
     *
     * @return void
     */
    public function paginate(int $perPage = 20)
    {
        $this->perPage = $perPage;

        $this->model()->setPerPage($perPage);
    }

    /**
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * @param string $paginator
     *
     * @return $this
     */
    public function setPaginatorClass(string $paginator)
    {
        $this->paginatorClass = $paginator;

        return $this;
    }

    /**
     * @param \Closure
     *
     * @return $this
     */
    public function pagination(\Closure $callback)
    {
        $this->paginationCallback = $callback;

        return $this;
    }

    /**
     * Get the grid paginator.
     *
     * @return mixed
     */
    public function paginator()
    {
        return $this->paginator ?: ($this->paginator = new $this->paginatorClass($this));
    }

    /**
     * If this grid use pagination.
     *
     * @return bool
     */
    public function allowPagination()
    {
        return $this->options['pagination'];
    }

    /**
     * Set per-page options.
     *
     * @param array $perPages
     */
    public function perPages(array $perPages)
    {
        $this->perPages = $perPages;

        return $this;
    }

    /**
     * @return $this
     */
    public function disablePerPages()
    {
        return $this->perPages([]);
    }

    /**
     * Get per-page options.
     *
     * @return array
     */
    public function getPerPages()
    {
        return $this->perPages;
    }

    /**
     * Disable grid pagination.
     *
     * @return $this
     */
    public function disablePagination(bool $disable = true)
    {
        $this->model->usePaginate(! $disable);

        return $this->option('pagination', ! $disable);
    }

    /**
     * Show grid pagination.
     *
     * @param bool $val
     *
     * @return $this
     */
    public function showPagination(bool $val = true)
    {
        return $this->disablePagination(! $val);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View|string
     */
    public function renderPagination()
    {
        if ($callback = $this->paginationCallback) {
            return $callback($this);
        }

        return view('admin::grid.table-pagination', ['grid' => $this]);
    }
}
