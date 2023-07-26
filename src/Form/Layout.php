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

    protected $hasBlock = false;

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

    public function appendToLastColumn($content)
    {
        if ($end = end($this->columns)) {
            foreach (is_array($content) ? $content : [$content] as $value) {
                $end->append($value);
            }
        }
    }

    public function hasBlocks()
    {
        return $this->hasBlock;
    }

    public function hasColumns()
    {
        return $this->hasColumn;
    }

    /**
     * 列布局.
     *
     * @param  int  $width  1~12
     * @param  mixed  $content
     */
    public function onlyColumn($width, $content)
    {
        $width = (int) ($width < 1 ? round(12 * $width) : $width);

        $this->hasColumn = true;

        $this->resetCurrentFields();

        $column = $this->column($width, $content);

        foreach ($this->currentFields as $field) {
            $column->append($field);
        }
    }

    /**
     * 增加列.
     *
     * @param  int  $width  1~12
     * @param  mixed  $content
     * @return Column
     */
    public function column(int $width, $content)
    {
        return $this->columns[] = new Column($content, $width);
    }

    /**
     * block布局.
     *
     * @param  int  $width
     * @param  \Closure  $callback
     */
    public function block(int $width, \Closure $callback)
    {
        $this->hasBlock = true;

        $this->column($width, function (Column $column) use ($callback) {
            $this->form->layoutColumn = $column;

            $column->row(function (\Dcat\Admin\Layout\Row $row) use ($callback) {
                $form = $this->form();

                $form->layoutRow = $row;

                $row->column(12, $form);

                $callback($form);
            });
        });
    }

    /**
     * @param  int  $width
     * @param  mixed  $content
     */
    public function prepend(int $width, $content)
    {
        $column = new Column($content, $width);

        array_unshift($this->columns, $column);
    }

    /**
     * @param  \Closure|null  $callback
     * @return BlockForm
     */
    public function form(\Closure $callback = null)
    {
        $form = new Form\BlockForm($this->form);

        $form->disableResetButton();
        $form->disableSubmitButton();
        $form->useFormTag(false);
        $form->ajax(false);

        if ($callback) {
            $callback($form);
        }

        return $form;
    }

    /**
     * Build html of content.
     *
     * @param  string  $add
     * @return string
     */
    public function build($add = null)
    {
        $html = '<div class="row">';

        foreach ($this->columns as $column) {
            $html .= $column->render();
        }

        return $html.'</div>'.$add;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function setColumns(array $columns)
    {
        $this->columns = $columns;

        return $this;
    }

    public function reset()
    {
        $this->hasColumn = false;

        $this->resetCurrentFields();

        $this->setColumns([]);
    }

    protected function resetCurrentFields()
    {
        $this->currentFields = [];
    }
}
