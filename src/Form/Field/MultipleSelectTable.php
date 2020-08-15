<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;

class MultipleSelectTable extends SelectTable
{
    protected $view = 'admin::form.selecttable';

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

    public function render()
    {
        Admin::css('@select2');

        return parent::render();
    }

    protected function addScript()
    {
        $this->script .= <<<JS
Dcat.grid.SelectTable({
    modal: replaceNestedFormIndex('#{$this->modal->getId()}'),
    container: '{$this->getElementClassSelector()}',
    input: replaceNestedFormIndex('#hidden-{$this->id}'),
    button: replaceNestedFormIndex('#{$this->getButtonId()}'),
    multiple: true,
    max: {$this->max},
    values: {$this->options},
})
JS;
    }
}
