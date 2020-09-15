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

    public function render()
    {
        $this->addVariables(['max' => $this->max]);

        return parent::render();
    }
}
