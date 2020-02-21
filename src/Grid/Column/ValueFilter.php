<?php

namespace Dcat\Admin\Grid\Column;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Support\Helper;

class ValueFilter
{
    /**
     * @var Column
     */
    protected $column;

    /**
     * @var string|\Closure
     */
    protected $operator;

    public function __construct(Column $column)
    {
        $this->column = $column;
    }

    public function setup($operator)
    {
        $this->operator = $operator;

        $this->addStyle();
        $this->addResetButton();
        $this->addApplyFilterCallback();
    }

    protected function addStyle()
    {
        Admin::style('.value-filter{border-bottom:1px dashed}.value-filter:hover>span{display:inline!important}');
    }

    protected function addResetButton()
    {
        $this->column->grid()->filtering(function () {
            if (! $this->value()) {
                return;
            }

            return $this->column->addHeader("&nbsp;<a class='fa fa-undo' href='{$this->resetUrl()}'></a>");
        });
    }

    protected function addApplyFilterCallback()
    {
        $this->column->grid()->filtering(function () {
            if (! ($value = $this->value())) {
               return;
            }
            $operator = $this->operator;
            $column = $this->column->getName();
            $model = $this->column->grid()->model();

            if (is_string($operator)) {
                $model->where($column, $operator, $value);
            } elseif ($operator instanceof \Closure) {
                $operator($model, $column, $value);
            }
        });
    }

    public function queryName()
    {
        $name = $this->column->grid()->getName();
        $column = $this->column->getName();

        return 'value-filter'.($name ? "-{$name}" : '')."-{$column}";
    }

    public function resetUrl()
    {
        return Helper::fullUrlWithoutQuery($this->queryName());
    }

    public function value()
    {
        return request($this->queryName());
    }

    public function render($value)
    {
        $original = $this->column->getOriginal();

        $url = request()->fullUrlWithQuery([$this->queryName() => $original]);

        return "<a class='value-filter' href='$url'>{$value}<span style='display:none'> &nbsp;<i class='fa fa-filter'></i></span></a>";
    }
}
