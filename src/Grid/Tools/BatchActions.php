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
     * Disable delete And Hode SelectAll Checkbox.
     *
     * @return $this
     */
    public function disableDeleteAndHodeSelectAll()
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
     * Scripts of BatchActions button groups.
     */
    protected function setupScript()
    {
        $name = $this->parent->getName();
        $allName = $this->parent->selectAllName();
        $rowName = $this->parent->rowName();

        $selected = trans('admin.grid_items_selected');

        $script = <<<JS
$('.{$rowName}-checkbox').on('change', function () {
    var btn = $('.{$allName}-btn');
    if (this.checked) {
        btn.show()
    } else {
        btn.hide()
    }
    setTimeout(function () {
         btn.find('.selected').html("{$selected}".replace('{n}', LA.grid.selectedRows('$name').length));
    }, 50)
});
JS;

        Admin::script($script);
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

        $this->setupScript();
        $this->prepareActions();

        $data = [
            'actions'                 => $this->actions,
            'selectAllName'           => $this->parent->selectAllName(),
            'isHoldSelectAllCheckbox' => $this->isHoldSelectAllCheckbox,
        ];

        return view('admin::grid.batch-actions', $data)->render();
    }
}
