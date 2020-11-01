<?php

namespace Dcat\Admin\Tree;

use Dcat\Admin\Actions\Action;
use Illuminate\Support\Str;

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

    public $selectorPrefix = '.tree-row-action-';

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

    /**
     * 生成选择器.
     * 需要保证每个行操作的选择器都不同.
     *
     * @param string $prefix
     * @param string $class
     *
     * @return string
     */
    public function makeSelector($prefix, $class = null)
    {
        $class = $class ?: static::class;

        $key = $prefix.'-'.$class.'-'.$this->getKey();

        if (! isset(static::$selectors[$key])) {
            static::$selectors[$key] = $prefix.Str::random(8);
        }

        return static::$selectors[$key];
    }
}
