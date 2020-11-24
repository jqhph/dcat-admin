<?php

namespace Dcat\Admin\Grid\Filter\Presenter;

use Dcat\Admin\Admin;

class MultipleSelectTable extends SelectTable
{
    public static $css = [
        '@select2',
    ];

    protected $view = 'admin::filter.selecttable';

    /**
     * @var int
     */
    protected $max = 0;

    /**
     * 设置最大选择数量.
     *
     * @param int $max
     *
     * @return $this
     */
    public function max(int $max)
    {
        $this->max = $max;

        return $this;
    }

    protected function addScript()
    {
        Admin::script(
            <<<JS
{$this->dialog->getScript()}

Dcat.grid.SelectTable({
    dialog: '#{$this->dialog->id()}',
    container: '#{$this->id}',
    input: '#hidden-{$this->id}',
    multiple: true,
    max: {$this->max},
    values: {$this->options},
});
JS
        );
    }
}
