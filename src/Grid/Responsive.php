<?php

namespace Dcat\Admin\Grid;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid;

/**
 * @see http://gergeo.se/RWD-Table-Patterns/#demo
 */
class Responsive
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var array
     */
    protected $options = ['addFocusBtn' => false];

    public function __construct(Grid $grid)
    {
        $this->grid = $grid;

        $this->options([
            'i18n' => [
                'focus'      => trans('admin.responsive.focus'),
                'display'    => trans('admin.responsive.display'),
                'displayAll' => trans('admin.responsive.display_all'),
            ],
        ]);
    }

    /**
     * Show focus button.
     *
     * @return $this
     */
    public function focus()
    {
        return $this->options(['addFocusBtn' => true]);
    }

    /**
     * @return $this
     */
    public function all()
    {
        $this->grid->columns()->each->responsive();

        return $this;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function options(array $options)
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    public function build()
    {
        Admin::css('vendor/dcat-admin/RWD-Table-Patterns/dist/css/rwd-table.min.css');
        Admin::js('vendor/dcat-admin/RWD-Table-Patterns/dist/js/rwd-table.min.js');

        $opt = json_encode($this->options);

        if (request()->pjax()) {
            Admin::script("$('.table-responsive').responsiveTable($opt);");
        } else {
            Admin::script("setTimeout(function(){ $('.table-responsive').responsiveTable($opt); },5);");
        }
    }
}
