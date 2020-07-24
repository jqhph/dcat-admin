<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Actions\Action;
use Dcat\Admin\Form;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;

class Actions extends AbstractDisplayer
{
    protected static $resolvedDialog;

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
     * @param string|Renderable|Action|Htmlable $action
     *
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
     * @param string|Renderable|Action|Htmlable $action
     *
     * @return $this
     */
    public function prepend($action)
    {
        $this->prepareAction($action);

        array_unshift($this->prepends, $action);

        return $this;
    }

    /**
     * @param mixed $action
     *
     * @return void
     */
    protected function prepareAction(&$action)
    {
        if ($action instanceof RowAction) {
            $action->setGrid($this->grid)
                ->setColumn($this->column)
                ->setRow($this->row);
        }
    }

    /**
     * Disable view action.
     *
     * @param bool $disable
     *
     * @return $this
     */
    public function disableView(bool $disable = true)
    {
        return $this->disableDefaultAction('view', $disable);
    }

    /**
     * Disable delete.
     *
     * @param bool $disable
     *
     * @return $this.
     */
    public function disableDelete(bool $disable = true)
    {
        return $this->disableDefaultAction('delete', $disable);
    }

    /**
     * Disable edit.
     *
     * @param bool $disable
     *
     * @return $this.
     */
    public function disableEdit(bool $disable = true)
    {
        return $this->disableDefaultAction('edit', $disable);
    }

    /**
     * Disable quick edit.
     *
     * @param bool $disable
     *
     * @return $this.
     */
    public function disableQuickEdit(bool $disable = true)
    {
        return $this->disableDefaultAction('quickEdit', $disable);
    }

    /**
     * @param string $key
     * @param bool $disable
     *
     * @return $this
     */
    protected function disableDefaultAction(string $key, bool $disable)
    {
        $this->actions[$key] = ! $disable;

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
    public function resource()
    {
        return $this->resource ?: parent::resource();
    }

    /**
     * @return void
     */
    protected function resetDefaultActions()
    {
        $this->disableView(! $this->grid->option('show_view_button'));
        $this->disableEdit(! $this->grid->option('show_edit_button'));
        $this->disableQuickEdit(! $this->grid->option('show_quick_edit_button'));
        $this->disableDelete(! $this->grid->option('show_delete_button'));
    }

    /**
     * @param array $callbacks
     *
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
        $label = trans('admin.show');

        return <<<EOT
<a href="{$this->resource()}/{$this->getKey()}" title="{$label}">
    <i class="feather icon-eye grid-action-icon"></i>
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
        $label = trans('admin.edit');

        return <<<EOT
<a href="{$this->resource()}/{$this->getKey()}/edit" title="{$label}">
    <i class="feather icon-edit-1 grid-action-icon"></i>
</a>&nbsp;
EOT;
    }

    /**
     * @return string
     */
    protected function renderQuickEdit()
    {
        if (! static::$resolvedDialog) {
            static::$resolvedDialog = true;

            [$width, $height] = $this->grid->option('dialog_form_area');

            Form::dialog(trans('admin.edit'))
                ->click(".{$this->grid->getRowName()}-edit")
                ->dimensions($width, $height)
                ->success('Dcat.reload()');
        }

        $label = trans('admin.quick_edit');

        return <<<EOF
<a title="{$label}" class="{$this->grid->getRowName()}-edit" data-url="{$this->resource()}/{$this->getKey()}/edit" href="javascript:void(0);">
    <i class="feather icon-edit grid-action-icon"></i>
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
        $label = trans('admin.delete');

        return <<<EOT
<a title="{$label}" href="javascript:void(0);" data-message="ID - {$this->getKey()}" data-url="{$this->resource()}/{$this->getKey()}" data-action="delete">
    <i class="feather icon-trash grid-action-icon"></i>
</a>&nbsp;
EOT;
    }
}
