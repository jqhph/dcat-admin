<?php

namespace Dcat\Admin\Show;
use Dcat\Admin\Show;
use Illuminate\Contracts\Support\Renderable;

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
     * Fields in this row.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Default field width for appended field.
     *
     * @var int
     */
    protected $defaultFieldWidth = 12;

    /**
     * Row constructor.
     *
     * @param \Closure $callback
     * @param Show     $show
     */
    public function __construct(\Closure $callback, Show $show)
    {
        $this->callback = $callback;

        $this->show = $show;

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
     * Get fields of this row.
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Set width for a incomming field.
     *
     * @param int $width
     *
     * @return $this
     */
    public function width($width = 12)
    {
        $this->defaultFieldWidth = $width;

        return $this;
    }


    /**
     * Add field
     * @param $name
     *
     * @return \Dcat\Admin\Show\Field|\Illuminate\Support\Collection
     */
    public function __get($name)
    {
        $field = $this->show->__get($name);
        $this->fields[] = [
            'width'   => $this->defaultFieldWidth,
            'element' => $field,
        ];
        return $field;
    }

    /**
     * @param $method
     * @param $arguments
     *
     * @return \Dcat\Admin\Show\Field
     */
    public function __call($method, $arguments)
    {
        $field = $this->show->__call($method, $arguments);


        $this->fields[] = [
            'width'   => $this->defaultFieldWidth,
            'element' => $field,
        ];

        return $field;
    }


}
