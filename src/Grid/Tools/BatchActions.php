<?php

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Admin;
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
     * @var bool
     */
    protected $isHoldSelectAllCheckbox = false;

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
     * Disable delete And Hide SelectAll Checkbox.
     *
     * @return $this
     */
    public function disableDeleteAndHideSelectAll()
    {
        $this->enableDelete = false;

        $this->isHoldSelectAllCheckbox = true;

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
            'actions'                 => $this->actions,
            'selectAllName'           => $this->parent->getSelectAllName(),
            'isHoldSelectAllCheckbox' => $this->isHoldSelectAllCheckbox,
            'parent'                  => $this->parent,
        ];

        return Admin::view('admin::grid.batch-actions', $data);
    }
}
