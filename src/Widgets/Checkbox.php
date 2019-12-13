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
     * @param string $id
     *
     * @return $this
     */
    public function checked($id)
    {
        $this->checked = (array) $id;

        return $this;
    }

    /**
     * @param string|array $excepts
     *
     * @return $this
     */
    public function checkedAll($excepts = [])
    {
        return $this->checked(array_keys(Arr::except($this->options, $excepts)));
    }

    public function circle(bool $value = true)
    {
        $this->circle = $value;

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
