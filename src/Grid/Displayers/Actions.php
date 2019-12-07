<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Form;
use Dcat\Admin\Support\Helper;

class Actions extends AbstractDisplayer
{
    protected static $resolvedWindow;

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
    protected $actions = ['view', 'edit', 'quickEdit', 'delete'];

    /**
     * @var string
     */
    protected $resource;

    /**
     * Append a action.
     *
     * @param $action
     *
     * @return $this
     */
    public function append($action)
    {
        array_push($this->appends, $action);

        return $this;
    }

    /**
     * Prepend a action.
     *
     * @param $action
     *
     * @return $this
     */
    public function prepend($action)
    {
        array_unshift($this->prepends, $action);

        return $this;
    }

    /**
     * Disable view action.
     *
     * @return $this
     */
    public function disableView(bool $disable = true)
    {
        if ($disable) {
            array_delete($this->actions, 'view');
        } elseif (!in_array('view', $this->actions)) {
            array_push($this->actions, 'view');
        }

        return $this;
    }

    /**
     * Disable delete.
     *
     * @return $this.
     */
    public function disableDelete(bool $disable = true)
    {
        if ($disable) {
            array_delete($this->actions, 'delete');
        } elseif (!in_array('delete', $this->actions)) {
            array_push($this->actions, 'delete');
        }

        return $this;
    }

    /**
     * Disable edit.
     *
     * @return $this.
     */
    public function disableEdit(bool $disable = true)
    {
        if ($disable) {
            array_delete($this->actions, 'edit');
        } elseif (!in_array('edit', $this->actions)) {
            array_push($this->actions, 'edit');
        }

        return $this;
    }

    /**
     * Disable quick edit.
     *
     * @return $this.
     */
    public function disableQuickEdit(bool $disable = true)
    {
        if ($disable) {
            array_delete($this->actions, 'quickEdit');
        } elseif (!in_array('quickEdit', $this->actions)) {
            array_push($this->actions, 'quickEdit');
        }

        return $this;
    }

    /**
     * Set resource of current resource.
     *
     * @param $resource
     *
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
    public function getResource()
    {
        return $this->resource ?: parent::getResource();
    }

    /**
     * {@inheritdoc}
     */
    public function display(array $callbacks = [])
    {
        $this->disableView(!$this->grid->option('show_view_button'));
        $this->disableEdit(!$this->grid->option('show_edit_button'));
        $this->disableQuickEdit(!$this->grid->option('show_quick_edit_button'));
        $this->disableDelete(!$this->grid->option('show_delete_button'));

        foreach ($callbacks as $callback) {
            if ($callback instanceof \Closure) {
                $callback->call($this->row, $this);
            }
        }

        $map = [Helper::class, 'render'];

        $prepends = array_map($map, $this->prepends);
        $appends = array_map($map, $this->appends);
        $actions = &$prepends;

        foreach ($this->actions as $action) {
            $method = 'render'.ucfirst($action);
            array_push($actions, $this->{$method}());
        }

        $actions = array_merge($actions, $appends);

        return implode('', $actions);
    }

    /**
     * Render view action.
     *
     * @return string
     */
    protected function renderView()
    {
        return <<<EOT
<a href="{$this->getResource()}/{$this->getKey()}">
    <i class="ti-eye grid-action-icon"></i>
</a>&nbsp;
EOT;
    }

    /**
     * Render edit action.
     *
     * @return string
     */
    protected function renderEdit()
    {
        return <<<EOT
<a href="{$this->getResource()}/{$this->getKey()}/edit">
    <i class="ti-pencil-alt grid-action-icon"></i>
</a>&nbsp;
EOT;
    }

    /**
     * @return string
     */
    protected function renderQuickEdit()
    {
        if (!static::$resolvedWindow) {
            static::$resolvedWindow = true;

            list($width, $height) = $this->grid->option('dialog_form_area');

            Form::modal(trans('admin.edit'))
                ->click(".{$this->grid->getGridRowName()}-edit")
                ->dimensions($width, $height)
                ->success('LA.reload()')
                ->render();
        }

        return <<<EOF
<a class="{$this->grid->getGridRowName()}-edit" data-url="{$this->getResource()}/{$this->getKey()}/edit" href="javascript:void(0);">
    <i class=" fa fa-clone grid-action-icon"></i>
</a>&nbsp;
EOF;
    }

    /**
     * Render delete action.
     *
     * @return string
     */
    protected function renderDelete()
    {
        return <<<EOT
<a href="javascript:void(0);" data-url="{$this->getResource()}/{$this->getKey()}" data-action="delete">
    <i class="ti-trash grid-action-icon"></i>
</a>&nbsp;
EOT;
    }
}
