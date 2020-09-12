<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Support\Helper;

class MultipleSelectTable extends SelectTable
{
    public static $css = [
        '@select2',
    ];

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

    protected function addScript()
    {
        $this->script .= <<<JS
Dcat.grid.SelectTable({
    dialog: replaceNestedFormIndex('#{$this->dialog->id()}'),
    container: replaceNestedFormIndex('#{$this->getAttribute('id')}'),
    input: replaceNestedFormIndex('#hidden-{$this->id}'),
    multiple: true,
    max: {$this->max},
    values: {$this->options},
});
JS;
    }

    /**
     * 转化为数组格式保存.
     *
     * @param mixed $value
     *
     * @return array|mixed
     */
    public function prepareInputValue($value)
    {
        return Helper::array($value, true);
    }
}
