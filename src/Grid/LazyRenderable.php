<?php

namespace Dcat\Admin\Grid;

use Dcat\Admin\Grid;
use Dcat\Admin\Support\LazyRenderable as Renderable;

abstract class LazyRenderable extends Renderable
{
    /**
     * 是否启用简化模式.
     *
     * @var bool
     */
    protected $simple = false;

    /**
     * 创建表格.
     *
     * @return Grid
     */
    abstract public function grid(): Grid;

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return $this->prepare($this->grid())->render();
    }

    /**
     * 是否启用简化模式.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function simple(bool $value = true)
    {
        return $this->with('_simple_', $value);
    }

    /**
     * @param Grid $grid
     *
     * @return Grid
     */
    protected function prepare(Grid $grid)
    {
        if (! $grid->getName()) {
            $grid->setName($this->getDefaultName());
        }

        if ($this->allowSimpleMode()) {
            $grid->disableCreateButton();
            $grid->disablePerPages();
            $grid->disableBatchDelete();
            $grid->disableRefreshButton();

            $grid->toolsWithOutline(false);

            $grid->filter()
                ->panel()
                ->view('admin::filter.tile-container');

            $grid->rowSelector()->click();
        }

        return $grid;
    }

    /**
     * 判断是否启用简化模式.
     *
     * @return bool
     */
    public function allowSimpleMode()
    {
        return $this->simple || $this->_simple_;
    }

    /**
     * 获取默认名称.
     *
     * @return string
     */
    protected function getDefaultName()
    {
        return strtolower(str_replace('\\', '-', static::class));
    }
}
