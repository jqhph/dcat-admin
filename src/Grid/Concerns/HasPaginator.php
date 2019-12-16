<?php

namespace Dcat\Admin\Grid\Concerns;

use Dcat\Admin\Grid\Tools;

trait HasPaginator
{
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
     * Get the grid paginator.
     *
     * @return mixed
     */
    public function paginator()
    {
        if (! $this->options['show_pagination']) {
            return;
        }

        return new Tools\Paginator($this);
    }

    /**
     * If this grid use pagination.
     *
     * @return bool
     */
    public function allowPagination()
    {
        return $this->options['show_pagination'];
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

        return $this->option('show_pagination', ! $disable);
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
}
