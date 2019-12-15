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
     * Actions column display class.
     *
     * @var string
     */
    protected $actionsClass;

    /**
     * @param string $actionClass
     *
     * @return $this
     */
    public function setActionClass(string $actionClass)
    {
        if (is_subclass_of($actionClass, Grid\Displayers\Actions::class)) {
            $this->actionsClass = $actionClass;
        }

        return $this;
    }

    /**
     * Get action display class.
     *
     * @return \Illuminate\Config\Repository|mixed|string
     */
    public function getActionClass()
    {
        if ($this->actionsClass) {
            return $this->actionsClass;
        }

        if ($class = config('admin.grid.grid_action_class')) {
            return $class;
        }

        return Grid\Displayers\Actions::class;
    }

    /**
     * Set grid action callback or add actions.
     *
     * @param Closure|array|string|Renderable|Grid\RowAction $callback
     *
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
                    $actions->append($v);
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
        if (! $this->options['show_actions']) {
            return;
        }

        $this->addColumn('__actions__', trans('admin.action'))
            ->displayUsing($this->getActionClass(), [$this->actionsCallback]);
    }

    /**
     * Disable all actions.
     *
     * @return $this
     */
    public function disableActions(bool $disable = true)
    {
        return $this->option('show_actions', ! $disable);
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
     * @param bool $disable
     *
     * @return $this
     */
    public function disableEditButton(bool $disable = true)
    {
        $this->options['show_edit_button'] = ! $disable;

        return $this;
    }

    /**
     * Show edit.
     *
     * @param bool $val
     *
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
        $this->options['show_quick_edit_button'] = ! $disable;

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
     * @param bool $disable
     *
     * @return $this
     */
    public function disableViewButton(bool $disable = true)
    {
        $this->options['show_view_button'] = ! $disable;

        return $this;
    }

    /**
     * Show view action.
     *
     * @param bool $disable
     *
     * @return $this
     */
    public function showViewButton(bool $val = true)
    {
        return $this->disableViewButton(! $val);
    }

    /**
     * Disable delete.
     *
     * @param bool $disable
     *
     * @return $this
     */
    public function disableDeleteButton(bool $disable = true)
    {
        $this->options['show_delete_button'] = ! $disable;

        return $this;
    }

    /**
     * Show delete button.
     *
     * @param bool $disable
     *
     * @return $this
     */
    public function showDeleteButton(bool $val = true)
    {
        return $this->disableDeleteButton(! $val);
    }
}
