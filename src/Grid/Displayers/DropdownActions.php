<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid\Actions\Delete;
use Dcat\Admin\Grid\Actions\Edit;
use Dcat\Admin\Grid\Actions\QuickEdit;
use Dcat\Admin\Grid\Actions\Show;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\Color;

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
    protected $defaultClass = [
        'view'      => Show::class,
        'edit'      => Edit::class,
        'quickEdit' => QuickEdit::class,
        'delete'    => Delete::class,
    ];

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
        foreach ($this->actions as $action => $enable) {
            if (! $enable) {
                continue;
            }

            $action = new $this->defaultClass[$action]();

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

        $this->addScript();

        $this->call($callbacks);

        $this->prependDefaultActions();

        $actions = [
            'default' => $this->default,
            'custom'  => $this->appends,
        ];

        return view('admin::grid.dropdown-actions', $actions);
    }
}
