<?php

namespace Dcat\Admin\Grid;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Column\Help;
use Dcat\Admin\Widgets\Widget;
use Illuminate\Support\Collection;

class ComplexHeader extends Widget
{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var string
     */
    protected $column;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var array
     */
    protected $columnNames = [];

    /**
     * @var array
     */
    protected $html = [];

    public function __construct(Grid $grid, ?string $column, array $columnNames, ?string $label = null)
    {
        $this->grid = $grid;
        $this->column = $column;
        $this->label = $label ?: admin_trans_field($column);
        $this->columnNames = collect($columnNames);

        $this->addDefaultAttributes();
    }

    /**
     * @return Collection
     */
    public function getColumnNames()
    {
        return $this->columnNames;
    }

    /**
     * @return Collection
     */
    public function columns()
    {
        return $this->columnNames->map(function ($name) {
            return $this->grid->allColumns()->get($name);
        })->filter();
    }

    /**
     * 默认隐藏字段.
     *
     * @return $this
     */
    public function hide()
    {
        $this->grid->hideColumns($this->column);

        return $this;
    }

    public function getName()
    {
        return $this->column;
    }

    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $html
     *
     * @return $this
     */
    public function append($html)
    {
        $this->html[] = $html;

        return $this;
    }

    /**
     * @param string|\Closure $message
     * @param null|string     $style     'green', 'blue', 'red', 'purple'
     * @param null|string     $placement 'bottom', 'left', 'right', 'top'
     *
     * @return $this
     */
    public function help($message, ?string $style = null, ?string $placement = null)
    {
        return $this->append((new Help($message, $style, $placement))->render());
    }

    protected function addDefaultAttributes()
    {
        $count = $this->columnNames->count();

        if ($count == 1) {
            $this->htmlAttributes['rowspan'] = 2;
        } else {
            $this->htmlAttributes['colspan'] = $count;
        }
    }

    public function render()
    {
        $headers = implode(' ', $this->html);

        return "<th {$this->formatHtmlAttributes()}>{$this->label}<span class='grid-column-header'>{$headers}</span></th>";
    }
}
