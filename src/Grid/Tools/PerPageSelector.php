<?php

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid;

class PerPageSelector extends AbstractTool
{
    /**
     * @var string
     */
    protected $perPage;

    /**
     * @var string
     */
    protected $perPageName = '';

    /**
     * Create a new PerPageSelector instance.
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;

        $this->initialize();
    }

    /**
     * Do initialize work.
     *
     * @return void
     */
    protected function initialize()
    {
        $this->perPageName = $this->grid->model()->getPerPageName();

        $this->perPage = (int) app('request')->input(
            $this->perPageName,
            $this->grid->getPerPage()
        );
    }

    /**
     * Get options for selector.
     *
     * @return static
     */
    public function getOptions()
    {
        return collect($this->grid->getPerPages())
            ->push($this->grid->getPerPage())
            ->push($this->perPage)
            ->unique()
            ->sort();
    }

    /**
     * Render PerPageSelector。
     *
     * @return string
     */
    public function render()
    {
        Admin::script($this->script());

        $options = $this->getOptions()->map(function ($option) {
            $selected = ($option == $this->perPage) ? 'selected' : '';
            $url = app('request')->fullUrlWithQuery([$this->perPageName => $option]);

            return "<option value=\"$url\" $selected>$option</option>";
        })->implode("\r\n");

        $show = trans('admin.show');
        $entries = trans('admin.entries');

        return <<<EOT

<label class="control-label pull-right hidden-xs" style="margin-right: 10px; font-weight: 100;">
        <small>$show</small>&nbsp;
        <select class="input-sm form-shadow {$this->grid->getPerPageName()}" name="per-page">
            $options
        </select>
        &nbsp;<small>$entries</small>
    </label>
EOT;
    }

    /**
     * Script of PerPageSelector.
     *
     * @return string
     */
    protected function script()
    {
        return <<<EOT
$('.{$this->grid->getPerPageName()}').change(function() {
    LA.reload(this.value);
});
EOT;
    }
}
