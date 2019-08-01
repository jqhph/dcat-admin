<?php

namespace Dcat\Admin\Grid\Concerns;

use Closure;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Displayers;

trait Actions
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
     * Set grid action callback.
     *
     * @param Closure $callback
     *
     * @return $this
     */
    public function actions(Closure $callback)
    {
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
        if (!$this->options['show_actions']) {
            return;
        }

        $this->addColumn('__actions__', trans('admin.action'))
            ->displayUsing($this->getActionClass(), [$this->actionsCallback]);
    }
}