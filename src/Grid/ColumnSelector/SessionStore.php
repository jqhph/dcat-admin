<?php

namespace Dcat\Admin\Grid\ColumnSelector;

use Dcat\Admin\Contracts\Grid\ColumnSelectorStore;
use Dcat\Admin\Grid;

class SessionStore implements ColumnSelectorStore
{
    /**
     * @var Grid
     */
    protected $grid;

    public function setGrid(Grid $grid)
    {
        $this->grid = $grid;
    }

    public function store(array $input)
    {
        session()->put($this->getVisibleColumnsKey(), $input);
    }

    public function get()
    {
        return session()->get($this->getVisibleColumnsKey());
    }

    public function forget()
    {
        session()->remove($this->getVisibleColumnsKey());
    }

    protected function getVisibleColumnsKey()
    {
        return $this->grid->getName().'/'.request()->path();
    }
}
