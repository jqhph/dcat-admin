<?php

namespace Dcat\Admin\Show;

use Dcat\Admin\Show;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;

class Row implements Renderable
{
    /**
     * Callback for add field to current row.s.
     *
     * @var \Closure
     */
    protected $callback;

    /**
     * Parent show.
     *
     * @var Show
     */
    protected $show;

    /**
     * @var Collection
     */
    protected $fields;

    /**
     * Default field width for appended field.
     *
     * @var int
     */
    protected $defaultFieldWidth = 12;

    /**
     * Row constructor.
     *
     * @param  \Closure  $callback
     * @param  Show  $show
     */
    public function __construct(\Closure $callback, Show $show)
    {
        $this->callback = $callback;

        $this->show = $show;

        $this->fields = new Collection();

        call_user_func($this->callback, $this);
    }

    /**
     * Render the row.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        return view('admin::show.row', ['fields' => $this->fields]);
    }

    /**
     * @return Collection|\Dcat\Admin\Show\Field[]
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * Set width for a incomming field.
     *
     * @param  int  $width
     * @return $this
     */
    public function width($width = 12)
    {
        $this->defaultFieldWidth = $width;

        return $this;
    }

    /**
     * Add field.
     *
     * @param  string  $name
     * @param  string  $label
     * @return \Dcat\Admin\Show\Field
     */
    public function field($name, $label = '')
    {
        $field = $this->show->field($name, $label);

        $this->pushField($field);

        return $field;
    }

    /**
     * Add field.
     *
     * @param $name
     * @return \Dcat\Admin\Show\Field|Collection
     */
    public function __get($name)
    {
        $field = $this->show->field($name);

        $this->pushField($field);

        return $field;
    }

    /**
     * @param $method
     * @param $arguments
     * @return \Dcat\Admin\Show\Field
     */
    public function __call($method, $arguments)
    {
        $field = $this->show->__call($method, $arguments);

        $this->pushField($field);

        return $field;
    }

    /**
     * @param  \Dcat\Admin\Show\Field  $field
     * @return void
     */
    protected function pushField($field)
    {
        $this->fields->push([
            'width'   => $this->defaultFieldWidth,
            'element' => $field,
        ]);
    }
}
