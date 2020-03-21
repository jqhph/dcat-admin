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
        Admin::collectAssets('rwd-table');

        $opt = json_encode($this->options);

        // 这里需要延迟执行，否则可能会造成页面元素跳跃闪动
        Admin::script("setTimeout(function(){ $('#{$this->grid->getTableId()}').parent().responsiveTable($opt); },350);");
    }
}
