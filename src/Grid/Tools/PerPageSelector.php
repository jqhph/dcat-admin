<?php

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Dcat\Admin\Widgets\Dropdown;
use Illuminate\Contracts\Support\Renderable;

class PerPageSelector implements Renderable
{
    /**
     * @var Grid
     */
    protected $parent;

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
        $this->parent = $grid;

        $this->initialize();
    }

    /**
     * Do initialize work.
     *
     * @return void
     */
    protected function initialize()
    {
        $this->perPageName = $this->parent->model()->getPerPageName();

        $this->perPage = (int) app('request')->input(
            $this->perPageName,
            $this->parent->getPerPage()
        );
    }

    /**
     * Get options for selector.
     *
     * @return static
     */
    public function getOptions()
    {
        return collect($this->parent->getPerPages())
            ->push($this->parent->getPerPage())
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
            $url = app('request')->fullUrlWithQuery([$this->perPageName => $option]);

            return "<a href=\"{$url}\">$option</a>";
        })->toArray();

        $dropdown = Dropdown::make($options)
            ->up()
            ->button($this->perPage)
            ->render();

        return <<<EOT
<label class="pull-right d-none d-sm-inline" style="margin-right: 10px">
    $dropdown
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
        return <<<JS
$('.{$this->parent->getPerPageName()}').change(function() {
    Dcat.reload(this.value);
});
JS;
    }
}
