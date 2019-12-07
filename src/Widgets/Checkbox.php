<?php

namespace Dcat\Admin\Widgets;

use Illuminate\Support\Arr;

class Checkbox extends Radio
{
    protected $view = 'admin::widgets.checkbox';

    protected $type = 'checkbox';

    protected $circle = true;

    protected $checked = [];

    /**
     * @param $id
     * @return $this
     */
    public function checked($id)
    {
        $this->checked = (array)$id;

        return $this;
    }

    /**
     * @param $excepts
     * @return Checkbox
     */
    public function checkedAll($excepts = [])
    {
        return $this->checked(array_keys(Arr::except($this->options, $excepts)));
    }

    public function circle(bool $flag)
    {
        $this->circle = $flag;

        return $this;
    }

    public function square()
    {
        return $this->circle(false);
    }


    public function variables()
    {
        $v = parent::variables();

        $v['circle'] = $this->circle ? 'checkbox-circle' : '';

        return $v;
    }
}
