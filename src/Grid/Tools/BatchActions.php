<?php

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Grid\BatchAction;
use Illuminate\Support\Collection;

class BatchActions extends AbstractTool
{
    /**
     * @var Collection
     */
    protected $actions;

    /**
     * @var bool
     */
    protected $enableDelete = true;

    /**
     * BatchActions constructor.
     */
    public function __construct()
    {
        $this->actions = new Collection();

        $this->appendDefaultAction();
    }

    /**
     * Append default action(batch delete action).
     *
     * return void
     */
    protected function appendDefaultAction()
    {
        $this->add(new BatchDelete(trans('admin.delete')));
    }

    /**
     * Disable delete.
     *
     * @return $this
     */
    public function disableDelete(bool $disable = true)
    {
        $this->enableDelete = ! $disable;

        return $this;
    }

    /**
     * Add a batch action.
     *
     * @param BatchAction $action
     *
     * @return $this
     */
    public function add(BatchAction $action)
    {
        $action->selectorPrefix = '.grid-batch-action-'.$this->actions->count();

        $this->actions->push($action);

        return $this;
    }

    /**
     * Prepare batch actions.
     *
     * @return void
     */
    protected function prepareActions()
    {
        foreach ($this->actions as $action) {
            $action->setGrid($this->parent);
        }
    }

    /**
     * Render BatchActions button groups.
     *
     * @return string
     */
    public function render()
    {
        if (! $this->enableDelete) {
            $this->actions->shift();
        }

        if ($this->actions->isEmpty()) {
            return '';
        }

        $this->prepareActions();

        $data = [
            'actions' => $this->actions,
        ];

        return view('admin::grid.batch-actions', $data)->render();
    }
}
