<?php

namespace Dcat\Admin\Grid\Concerns;

use Closure;
use Dcat\Admin\Grid;
use Illuminate\Contracts\Support\Renderable;

trait HasActions
{
    /**
     * Callback for grid actions.
     *
     * @var Closure[]
     */
    protected $actionsCallback = [];

    /**
     * @param  string  $actionClass
     * @return $this
     */
    public function setActionClass(string $actionClass)
    {
        $this->options['actions_class'] = $actionClass;

        return $this;
    }

    /**
     * Get action display class.
     *
     * @return \Illuminate\Config\Repository|mixed|string
     */
    public function getActionClass()
    {
        if ($this->options['actions_class']) {
            return $this->options['actions_class'];
        }

        if ($class = config('admin.grid.grid_action_class')) {
            return $class;
        }

        return Grid\Displayers\Actions::class;
    }

    /**
     * Set grid action callback or add actions.
     *
     * @param  Closure|array|string|Renderable|Grid\RowAction  $callback
     * @return $this
     */
    public function actions($callback)
    {
        if (! $callback instanceof Closure) {
            $action = $callback;

            $callback = function (Grid\Displayers\Actions $actions) use (&$action) {
                if (! is_array($action)) {
                    $action = [$action];
                }

                foreach ($action as $v) {
                    $actions->append(clone $v);
                }
            };
        }

        $this->actionsCallback[] = $callback;

        return $this;
    }

    /**
     * Add `actions` column for grid.
     *
     * @return void
     */
    protected function appendActionsColumn()
    {
        if (! $this->options['actions']) {
            return;
        }

        $attributes = ['class' => 'grid__actions__'];

        $this->addColumn(Grid\Column::ACTION_COLUMN_NAME, trans('admin.action'))
            ->setHeaderAttributes($attributes)
            ->setAttributes($attributes)
            ->displayUsing($this->getActionClass(), [$this->actionsCallback]);
    }

    /**
     * Disable all actions.
     *
     * @return $this
     */
    public function disableActions(bool $disable = true)
    {
        return $this->option('actions', ! $disable);
    }

    /**
     * Show all actions.
     *
     * @return $this
     */
    public function showActions(bool $val = true)
    {
        return $this->disableActions(! $val);
    }

    /**
     * Disable edit.
     *
     * @param  bool  $disable
     * @return $this
     */
    public function disableEditButton(bool $disable = true)
    {
        $this->options['edit_button'] = ! $disable;

        return $this;
    }

    /**
     * Show edit.
     *
     * @param  bool  $val
     * @return $this
     */
    public function showEditButton(bool $val = true)
    {
        return $this->disableEditButton(! $val);
    }

    /**
     * Disable quick edit.
     *
     * @return $this.
     */
    public function disableQuickEditButton(bool $disable = true)
    {
        $this->options['quick_edit_button'] = ! $disable;

        return $this;
    }

    /**
     * Show quick edit button.
     *
     * @return $this.
     */
    public function showQuickEditButton(bool $val = true)
    {
        return $this->disableQuickEditButton(! $val);
    }

    /**
     * Disable view action.
     *
     * @param  bool  $disable
     * @return $this
     */
    public function disableViewButton(bool $disable = true)
    {
        $this->options['view_button'] = ! $disable;

        return $this;
    }

    /**
     * Show view action.
     *
     * @param  bool  $disable
     * @return $this
     */
    public function showViewButton(bool $val = true)
    {
        return $this->disableViewButton(! $val);
    }

    /**
     * Disable delete.
     *
     * @param  bool  $disable
     * @return $this
     */
    public function disableDeleteButton(bool $disable = true)
    {
        $this->options['delete_button'] = ! $disable;

        return $this;
    }

    /**
     * Show delete button.
     *
     * @param  bool  $disable
     * @return $this
     */
    public function showDeleteButton(bool $val = true)
    {
        return $this->disableDeleteButton(! $val);
    }
}
