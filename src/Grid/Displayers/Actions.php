<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Actions\Action;
use Dcat\Admin\Grid\Actions\Delete;
use Dcat\Admin\Grid\Actions\Edit;
use Dcat\Admin\Grid\Actions\QuickEdit;
use Dcat\Admin\Grid\Actions\Show;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Traits\Macroable;

class Actions extends AbstractDisplayer
{
    use Macroable;

    /**
     * @var array
     */
    protected $appends = [];

    /**
     * @var array
     */
    protected $prepends = [];

    /**
     * Default actions.
     *
     * @var array
     */
    protected $actions = [
        'view'      => true,
        'edit'      => true,
        'quickEdit' => false,
        'delete'    => true,
    ];

    /**
     * @var string
     */
    protected $resource;

    /**
     * Append a action.
     *
     * @param  string|Renderable|Action|Htmlable  $action
     * @return $this
     */
    public function append($action)
    {
        $this->prepareAction($action);

        array_push($this->appends, $action);

        return $this;
    }

    /**
     * Prepend a action.
     *
     * @param  string|Renderable|Action|Htmlable  $action
     * @return $this
     */
    public function prepend($action)
    {
        $this->prepareAction($action);

        array_unshift($this->prepends, $action);

        return $this;
    }

    /**
     * @param  mixed  $action
     * @return mixed
     */
    protected function prepareAction(&$action)
    {
        if ($action instanceof RowAction) {
            $action->setGrid($this->grid)
                ->setColumn($this->column)
                ->setRow($this->row);
        }

        return $action;
    }

    public function view(bool $value = true)
    {
        return $this->setAction('view', $value);
    }

    /**
     * Disable view action.
     *
     * @param  bool  $disable
     * @return $this
     */
    public function disableView(bool $disable = true)
    {
        return $this->setAction('view', ! $disable);
    }

    public function delete(bool $value = true)
    {
        return $this->setAction('delete', $value);
    }

    /**
     * Disable delete.
     *
     * @param  bool  $disable
     * @return $this.
     */
    public function disableDelete(bool $disable = true)
    {
        return $this->setAction('delete', ! $disable);
    }

    public function edit(bool $value = true)
    {
        return $this->setAction('edit', $value);
    }

    /**
     * Disable edit.
     *
     * @param  bool  $disable
     * @return $this.
     */
    public function disableEdit(bool $disable = true)
    {
        return $this->setAction('edit', ! $disable);
    }

    public function quickEdit(bool $value = true)
    {
        return $this->setAction('quickEdit', $value);
    }

    /**
     * Disable quick edit.
     *
     * @param  bool  $disable
     * @return $this.
     */
    public function disableQuickEdit(bool $disable = true)
    {
        return $this->setAction('quickEdit', ! $disable);
    }

    /**
     * @param  string  $key
     * @param  bool  $disable
     * @return $this
     */
    protected function setAction(string $key, bool $value)
    {
        $this->actions[$key] = $value;

        return $this;
    }

    /**
     * Set resource of current resource.
     *
     * @param $resource
     * @return $this
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Get resource of current resource.
     *
     * @return string
     */
    public function resource()
    {
        return $this->resource ?: parent::resource();
    }

    /**
     * @return void
     */
    protected function resetDefaultActions()
    {
        $this->view($this->grid->option('view_button'));
        $this->edit($this->grid->option('edit_button'));
        $this->quickEdit($this->grid->option('quick_edit_button'));
        $this->delete($this->grid->option('delete_button'));
    }

    /**
     * @param  array  $callbacks
     * @return void
     */
    protected function call(array $callbacks = [])
    {
        foreach ($callbacks as $callback) {
            if ($callback instanceof \Closure) {
                $callback->call($this->row, $this);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function display(array $callbacks = [])
    {
        $this->resetDefaultActions();

        $this->call($callbacks);

        $toString = [Helper::class, 'render'];

        $prepends = array_map($toString, $this->prepends);
        $appends = array_map($toString, $this->appends);

        foreach ($this->actions as $action => $enable) {
            if ($enable) {
                $method = 'render'.ucfirst($action);
                array_push($prepends, $this->{$method}());
            }
        }

        return implode('', array_merge($prepends, $appends));
    }

    /**
     * Render view action.
     *
     * @return string
     */
    protected function renderView()
    {
        $action = config('admin.grid.actions.view') ?: Show::class;
        $action = $action::make($this->getViewLabel());

        return $this->prepareAction($action);
    }

    /**
     * @return string
     */
    protected function getViewLabel()
    {
        $label = trans('admin.show');

        return "<i title='{$label}' class=\"feather icon-eye grid-action-icon\"></i> &nbsp;";
    }

    /**
     * Render edit action.
     *
     * @return string
     */
    protected function renderEdit()
    {
        $action = config('admin.grid.actions.edit') ?: Edit::class;
        $action = $action::make($this->getEditLabel());

        return $this->prepareAction($action);
    }

    /**
     * @return string
     */
    protected function getEditLabel()
    {
        $label = trans('admin.edit');

        return "<i title='{$label}' class=\"feather icon-edit-1 grid-action-icon\"></i> &nbsp;";
    }

    /**
     * @return string
     */
    protected function renderQuickEdit()
    {
        $action = config('admin.grid.actions.quick_edit') ?: QuickEdit::class;
        $action = $action::make($this->getQuickEditLabel());

        return $this->prepareAction($action);
    }

    /**
     * @return string
     */
    protected function getQuickEditLabel()
    {
        $label = trans('admin.quick_edit');

        return "<i title='{$label}' class=\"feather icon-edit grid-action-icon\"></i> &nbsp;";
    }

    /**
     * Render delete action.
     *
     * @return string
     */
    protected function renderDelete()
    {
        $action = config('admin.grid.actions.delete') ?: Delete::class;
        $action = $action::make($this->getDeleteLabel());

        return $this->prepareAction($action);
    }

    /**
     * @return string
     */
    protected function getDeleteLabel()
    {
        $label = trans('admin.delete');

        return "<i class=\"feather icon-trash grid-action-icon\" title='{$label}'></i> &nbsp;";
    }
}
