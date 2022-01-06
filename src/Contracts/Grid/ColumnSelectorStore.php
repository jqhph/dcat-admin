<?php

namespace Dcat\Admin\Contracts\Grid;

use Dcat\Admin\Grid;

interface ColumnSelectorStore
{
    /**
     * @param  Grid  $grid
     * @return mixed
     */
    public function setGrid(Grid $grid);

    /**
     * 存储数据.
     *
     * @param  array  $input
     * @return void
     */
    public function store(array $input);

    /**
     * 获取数据.
     *
     * @return array|null
     */
    public function get();

    /**
     * 移除.
     *
     * @return void
     */
    public function forget();
}
