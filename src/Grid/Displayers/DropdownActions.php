<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Grid\Actions\Delete;
use Dcat\Admin\Grid\Actions\Edit;
use Dcat\Admin\Grid\Actions\QuickEdit;
use Dcat\Admin\Grid\Actions\Show;
use Dcat\Admin\Support\Helper;

class DropdownActions extends Actions
{
    /**
     * @var array
     */
    protected $default = [];

    /**
     * @var array
     */
    protected $defaultActions = [
        'view'      => Show::class,
        'edit'      => Edit::class,
        'quickEdit' => QuickEdit::class,
        'delete'    => Delete::class,
    ];

    public function prepend($action)
    {
        return $this->append($action);
    }

    /**
     * @param mixed $action
     *
     * @return void
     */
    protected function prepareAction(&$action)
    {
        parent::prepareAction($action);

        $action = $this->wrapCustomAction($action);
    }

    /**
     * @param mixed $action
     *
     * @return string
     */
    protected function wrapCustomAction($action)
    {
        $action = Helper::render($action);

        if (mb_strpos($action, '</a>') === false) {
            return "<a>$action</a>";
        }

        return $action;
    }

    /**
     * Prepend default `edit` `view` `delete` actions.
     */
    protected function prependDefaultActions()
    {
        foreach ($this->actions as $action => $enable) {
            if (! $enable) {
                continue;
            }

            $action = new $this->defaultActions[$action]();

            $this->prepareAction($action);

            array_push($this->default, $action);
        }
    }

    /**
     * @param \Closure[] $callback
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function display(array $callbacks = [])
    {
        $this->resetDefaultActions();

        $this->call($callbacks);

        $this->prependDefaultActions();

        $actions = [
            'default'  => $this->default,
            'custom'   => $this->appends,
            'selector' => ".{$this->grid->getRowName()}-checkbox",
        ];

        return view('admin::grid.dropdown-actions', $actions);
    }
}
