<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Support\Helper;
use Illuminate\Support\Arr;

class Checkbox extends Radio
{
    protected $view = 'admin::widgets.checkbox';
    protected $type = 'checkbox';
    protected $checked = [];

    /**
     * 设置选中的的选项.
     *
     * @param string|array $options
     *
     * @return $this
     */
    public function check($options)
    {
        $this->checked = Helper::array($options);

        return $this;
    }

    /**
     * 选中所有选项.
     *
     * @param string|array $excepts
     *
     * @return $this
     */
    public function checkAll($excepts = [])
    {
        return $this->check(
            array_keys(Arr::except($this->options, $excepts))
        );
    }
}
