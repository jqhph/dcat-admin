<?php

namespace Dcat\Admin\Form;

use Dcat\Admin\Form;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Widgets\Form as WidgetForm;

class Layout
{
    /**
     * @var Form|WidgetForm
     */
    protected $form;

    /**
     * @var Column[]
     */
    protected $columns = [];

    /**
     * @var array
     */
    protected $currentFields = [];

    /**
     * @var bool
     */
    protected $hasColumn = false;

    public function __construct($form)
    {
        $this->form = $form;
    }

    public function addField(Field $field)
    {
        $this->currentFields[] = $field;
    }

    public function hasColumns()
    {
        return $this->hasColumn;
    }

    /**
     * @param int   $width   1~12
     * @param mixed $content
     */
    public function onlyColumn($width, $content)
    {
        $width = $width < 1 ? round(12 * $width) : $width;

        $this->hasColumn = true;

        $this->currentFields = [];

        $column = new Column($content, $width);

        $this->columns[] = $column;

        foreach ($this->currentFields as $field) {
            $column->append($field);
        }
    }

    /**
     * @param int   $width   1~12
     * @param mixed $content
     */
    public function column($width, $content)
    {
        $width = $width < 1 ? round(12 * $width) : $width;

        $this->columns[] = new Column($content, $width);
    }

    /**
     * @param int   $width
     * @param mixed $content
     */
    public function prepend(int $width, $content)
    {
        $column = new Column($content, $width);

        array_unshift($this->columns, $column);
    }

    /**
     * @param \Closure|null $callback
     *
     * @return BlockForm
     */
    public function form(\Closure $callback = null)
    {
        $form = new Form\BlockForm($this->form);

        $this->form->builder()->addForm($form);

        if ($callback) {
            $callback($form);
        }

        return $form;
    }

    /**
     * Build html of content.
     *
     * @return string
     */
    public function build()
    {
        $html = '<div class="row">';

        foreach ($this->columns as $column) {
            $html .= $column->render();
        }

        return $html.'</div>';
    }
}
