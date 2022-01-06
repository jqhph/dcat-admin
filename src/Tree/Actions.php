<?php

namespace Dcat\Admin\Tree;

use Dcat\Admin\Support\Helper;
use Dcat\Admin\Tree;
use Illuminate\Contracts\Support\Renderable;

class Actions implements Renderable
{
    /**
     * @var Tree
     */
    protected $parent;

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $row;

    /**
     * @var array
     */
    protected $appends = [];

    /**
     * @var array
     */
    protected $prepends = [];

    /**
     * @var array
     */
    protected $actions = [
        'delete'    => true,
        'quickEdit' => true,
        'edit'      => false,
    ];

    /**
     * @var array
     */
    protected $defaultActions = [
        'edit'      => Tree\Actions\Edit::class,
        'quickEdit' => Tree\Actions\QuickEdit::class,
        'delete'    => Tree\Actions\Delete::class,
    ];

    /**
     * @param  string|Renderable|\Dcat\Admin\Actions\Action|\Illuminate\Contracts\Support\Htmlable  $action
     * @return $this
     */
    public function append($action)
    {
        $this->prepareAction($action);

        array_push($this->appends, $action);

        return $this;
    }

    /**
     * @param  string|Renderable|\Dcat\Admin\Actions\Action|\Illuminate\Contracts\Support\Htmlable  $action
     * @return $this
     */
    public function prepend($action)
    {
        $this->prepareAction($action);

        array_unshift($this->prepends, $action);

        return $this;
    }

    public function getKey()
    {
        return $this->row->{$this->parent()->getKeyName()};
    }

    public function quickEdit(bool $value = true)
    {
        $this->actions['quickEdit'] = $value;

        return $this;
    }

    public function disableQuickEdit(bool $value = true)
    {
        return $this->quickEdit(! $value);
    }

    public function edit(bool $value = true)
    {
        $this->actions['edit'] = $value;

        return $this;
    }

    public function disableEdit(bool $value = true)
    {
        return $this->edit(! $value);
    }

    public function delete(bool $value = true)
    {
        $this->actions['delete'] = $value;

        return $this;
    }

    public function disableDelete(bool $value = true)
    {
        return $this->delete(! $value);
    }

    public function render()
    {
        $this->prependDefaultActions();

        $toString = [Helper::class, 'render'];

        $prepends = array_map($toString, $this->prepends);
        $appends = array_map($toString, $this->appends);

        return implode('', array_merge($prepends, $appends));
    }

    protected function prepareAction($action)
    {
        if ($action instanceof RowAction) {
            $action->setParent($this);
            $action->setRow($this->row);
        }
    }

    protected function prependDefaultActions()
    {
        foreach ($this->actions as $action => $enable) {
            if (! $enable) {
                continue;
            }

            $action = new $this->defaultActions[$action]();

            $this->prepareAction($action);

            $this->prepend($action);
        }
    }

    public function parent()
    {
        return $this->parent;
    }

    public function setParent(Tree $tree)
    {
        $this->parent = $tree;
    }

    public function getRow()
    {
        return $this->row;
    }

    public function setRow($row)
    {
        $this->row = $row;
    }
}
