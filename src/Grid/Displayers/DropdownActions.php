<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Support\Helper;

class DropdownActions extends Actions
{
    protected $view = 'admin::grid.dropdown-actions';

    /**
     * @var array
     */
    protected $default = [];

    public function prepend($action)
    {
        return $this->append($action);
    }

    /**
     * @param  mixed  $action
     * @return mixed
     */
    protected function prepareAction(&$action)
    {
        parent::prepareAction($action);

        return $action = $this->wrapCustomAction($action);
    }

    /**
     * @param  mixed  $action
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

            array_push($this->default, $this->{'render'.ucfirst($action)}());
        }
    }

    /**
     * @param  \Closure[]  $callback
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

        return view($this->view, $actions);
    }

    protected function getViewLabel()
    {
    }

    protected function getEditLabel()
    {
    }

    protected function getQuickEditLabel()
    {
    }

    protected function getDeleteLabel()
    {
    }
}
