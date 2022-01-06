<?php

namespace Dcat\Admin\Show;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Fluent;

abstract class AbstractField implements Renderable
{
    /**
     * Field value.
     *
     * @var mixed
     */
    protected $value;

    /**
     * Current field model.
     *
     * @var Fluent
     */
    protected $model;

    /**
     * If this field show with a border.
     *
     * @var bool
     */
    public $border = true;

    /**
     * If this field show escaped contents.
     *
     * @var bool
     */
    public $escape = false;

    /**
     * @param  mixed  $value
     * @return AbstractField $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @param  Fluent  $model
     * @return AbstractField $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return mixed
     */
    abstract public function render();
}
