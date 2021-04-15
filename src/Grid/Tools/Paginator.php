<?php

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Pagination\LengthAwarePaginator;

class Paginator implements Renderable
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var \Illuminate\Pagination\LengthAwarePaginator
     */
    public $paginator = null;

    /**
     * Create a new Paginator instance.
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;

        $this->initPaginator();
    }

    /**
     * Initialize work for Paginator.
     *
     * @return void
     */
    protected function initPaginator()
    {
        $this->paginator = $this->grid->model()->paginator();

        if ($this->paginator instanceof LengthAwarePaginator) {
            $this->paginator->appends(request()->all());
        }
    }

    /**
     * Get Pagination links.
     *
     * @return string
     */
    protected function paginationLinks()
    {
        return $this->paginator->render('admin::grid.pagination');
    }

    /**
     * Get per-page selector.
     *
     * @return string|null
     */
    protected function perPageSelector()
    {
        if (! $this->grid->getPerPages()) {
            return;
        }

        return (new PerPageSelector($this->grid))->render();
    }

    /**
     * Get range infomation of paginator.
     *
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    protected function paginationRanger()
    {
        $parameters = [
            'first' => $this->paginator->firstItem(),
            'last'  => $this->paginator->lastItem(),
            'total' => method_exists($this->paginator, 'total') ? $this->paginator->total() : '...',
        ];

        $parameters = collect($parameters)->flatMap(function ($parameter, $key) {
            return [$key => "<b>$parameter</b>"];
        });

        $color = Admin::color()->dark80();

        return "<span class='d-none d-sm-inline' style=\"line-height:33px;color:{$color}\">".trans('admin.pagination.range', $parameters->all()).'</span>';
    }

    /**
     * Render Paginator.
     *
     * @return string
     */
    public function render()
    {
        return $this->paginationRanger().
            $this->paginationLinks().
            $this->perPageSelector();
    }
}
