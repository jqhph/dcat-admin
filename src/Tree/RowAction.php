<?php

namespace Dcat\Admin\Tree;

use Dcat\Admin\Actions\Action;

class RowAction extends Action
{
    /**
     * @var \Dcat\Admin\Tree\Actions;
     */
    protected $actions;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $row;

    /**
     * 获取主键值.
     *
     * @return array|mixed|string
     */
    public function getKey()
    {
        if ($key = parent::getKey()) {
            return $key;
        }

        return $this->row->{$this->actions->parent()->getKeyName()};
    }

    /**
     * 获取行数据.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * 获取资源路径.
     *
     * @return string
     */
    public function resource()
    {
        return $this->actions->parent()->resource();
    }

    public function getActions()
    {
        return $this->actions;
    }

    public function setParent(Actions $actions)
    {
        $this->actions = $actions;
    }

    public function setRow($row)
    {
        $this->row = $row;
    }
}
