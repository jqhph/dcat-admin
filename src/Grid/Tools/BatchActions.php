<?php

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Admin;
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
        $id = $this->actions->count();

        $action->id($id);

        $this->actions->push($action);

        return $this;
    }

    /**
     * Setup scripts of batch actions.
     *
     * @return void
     */
    protected function setUpScripts()
    {
        foreach ($this->actions as $action) {
            $action->setGrid($this->grid);

            Admin::script($action->script());
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

        $this->setUpScripts();

        $data = [
            'actions' => $this->actions,
        ];

        return view('admin::grid.batch-actions', $data)->render();
    }
}
