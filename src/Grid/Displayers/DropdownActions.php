<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid\Actions\Delete;
use Dcat\Admin\Grid\Actions\Edit;
use Dcat\Admin\Grid\Actions\QuickEdit;
use Dcat\Admin\Grid\Actions\Show;
use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\Color;
use Illuminate\Contracts\Support\Renderable;

class DropdownActions extends Actions
{
    /**
     * @var array
     */
    protected $custom = [];

    /**
     * @var array
     */
    protected $default = [];

    /**
     * @var array
     */
    protected $defaultClass = [Edit::class, QuickEdit::class, Show::class, Delete::class];

    /**
     * Add JS script into pages.
     *
     * @return void.
     */
    protected function addScript()
    {
        $background = $this->grid->option('row_selector_bg') ?: Color::dark20();
        $checkbox = ".{$this->grid->rowName()}-checkbox";

        $script = <<<JS
$(function() {
  $('.table-responsive').on('shown.bs.dropdown', function(e) {
    var t = $(this),
      m = $(e.target).find('.dropdown-menu'),
      tb = t.offset().top + t.height(),
      mb = m.offset().top + m.outerHeight(true),
      d = 20; // Space for shadow + scrollbar.   
      
    if (t[0].scrollWidth > t.innerWidth()) {
      if (mb + d > tb) {
        t.css('padding-bottom', ((mb + d) - tb));
      }
    } else {
      t.css('overflow', 'visible');
    }
    
    $(e.target).parents('tr').css({'background-color': '{$background}'});
  }).on('hidden.bs.dropdown', function(e) {
    $(this).css({
      'padding-bottom': '',
      'overflow': ''
    });
    
    var tr = $(e.target).parents('tr').eq(0);
    
    if (! tr.find("{$checkbox}:checked").length) {
        tr.css({'background-color': ''});
    }
  });
});
JS;

        Admin::script($script);
    }

    /**
     * @param RowAction|string|Renderable $action
     *
     * @return $this
     */
    public function append($action)
    {
        if ($action instanceof RowAction) {
            $this->prepareAction($action);
        }

        array_push($this->custom, $this->wrapCustomAction($action));

        return $this;
    }

    public function prepend($action)
    {
        return $this->append($action);
    }

    /**
     * @param $action
     *
     * @return mixed
     */
    protected function wrapCustomAction($action)
    {
        $action = Helper::render($action);

        if (strpos($action, '</a>') === false) {
            return "<a>$action</a>";
        }

        return $action;
    }

    /**
     * Prepend default `edit` `view` `delete` actions.
     */
    protected function prependDefaultActions()
    {
        foreach ($this->defaultClass as $class) {
            /** @var RowAction $action */
            $action = new $class();

            $this->prepareAction($action);

            array_push($this->default, $action);
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
        if ($disable) {
            Helper::deleteByValue($this->defaultClass, Show::class);
        } elseif (! in_array(Show::class, $this->defaultClass)) {
            array_push($this->defaultClass, Show::class);
        }

        return $this;
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
        if ($disable) {
            Helper::deleteByValue($this->defaultClass, Delete::class);
        } elseif (! in_array(Delete::class, $this->defaultClass)) {
            array_push($this->defaultClass, Delete::class);
        }

        return $this;
    }

    /**
     * Disable edit.
     *
     * @param bool $disable
     *
     * @return $this
     */
    public function disableEdit(bool $disable = true)
    {
        if ($disable) {
            Helper::deleteByValue($this->defaultClass, Edit::class);
        } elseif (! in_array(Edit::class, $this->defaultClass)) {
            array_push($this->defaultClass, Edit::class);
        }

        return $this;
    }

    /**
     * Disable quick edit.
     *
     * @return $this.
     */
    public function disableQuickEdit(bool $disable = true)
    {
        if ($disable) {
            Helper::deleteByValue($this->defaultClass, QuickEdit::class);
        } elseif (! in_array(Show::class, $this->defaultClass)) {
            array_push($this->defaultClass, QuickEdit::class);
        }

        return $this;
    }

    /**
     * @param \Closure[] $callback
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function display($callbacks = [])
    {
        $this->disableView(! $this->grid->option('show_view_button'));
        $this->disableEdit(! $this->grid->option('show_edit_button'));
        $this->disableQuickEdit(! $this->grid->option('show_quick_edit_button'));
        $this->disableDelete(! $this->grid->option('show_delete_button'));

        $this->addScript();

        foreach ($callbacks as $callback) {
            if ($callback instanceof \Closure) {
                $callback->call($this->row, $this);
            }
        }

        $this->prependDefaultActions();

        $actions = [
            'default' => $this->default,
            'custom'  => $this->custom,
        ];

        return view('admin::grid.dropdown-actions', $actions);
    }
}
