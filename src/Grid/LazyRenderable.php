<?php

namespace Dcat\Admin\Grid;

use Dcat\Admin\Grid;
use Dcat\Admin\Support\LazyRenderable as Renderable;

abstract class LazyRenderable extends Renderable
{
    abstract public function grid(): Grid;

    public function render()
    {
        return $this->prepare($this->grid())->render();
    }

    protected function prepare(Grid $grid)
    {
        if (! $grid->getName()) {
            $grid->setName($this->getDefaultName());
        }

        $grid->disableCreateButton();
        $grid->disableActions();
        $grid->disablePerPages();
        $grid->disableBatchActions();
        $grid->disableRefreshButton();

        $grid->filter()
            ->panel()
            ->view('admin::filter.tile-container');

        $grid->rowSelector()->click();

        return $grid;
    }

    protected function getDefaultName()
    {
        return strtolower(str_replace('\\', '-', static::class));
    }
}
