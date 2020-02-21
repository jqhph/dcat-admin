<?php

namespace Dcat\Admin\Grid\Column;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid\Column;
use Dcat\Admin\Support\Helper;
use Illuminate\Support\Arr;

class ValueFilter
{
    /**
     * @var Column
     */
    protected $column;

    /**
     * @var string|\Closure
     */
    protected $valueKey;

    /**
     * @var string|\Closure
     */
    protected $operator;

    public function __construct(Column $column)
    {
        $this->column = $column;
    }

    public function setup($valueKey, $operator)
    {
        $this->valueKey = $valueKey;
        $this->operator = $operator;

        $this->addStyle();
        $this->addResetButton();
        $this->addApplyFilterCallback();
    }

    protected function addStyle()
    {
        Admin::style('.value-filter .dashed{border-bottom:1px dashed}.value-filter:hover+a{opacity:1!important}');
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
            $columnName = $this->column->getName();
            $model = $this->column->grid()->model();

            if (is_string($operator)) {
                $model->where($columnName, $operator, $value);
            } elseif ($operator instanceof \Closure) {
                $operator($model, $value, $columnName);
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

    protected function originalValue()
    {
        if (! $this->valueKey) {
            return $this->column->getOriginal();
        }

        $row = $this->column->getOriginalModel();

        if ($this->valueKey instanceof \Closure) {
            return $this->valueKey->call(
                $row,
                $this->column->getName()
            );
        }

        return Arr::get(
            $row->toArray(),
            $this->valueKey,
            $this->column->getOriginal()
        );
    }

    protected function wrap($value)
    {
        if (! preg_match('/<[^>]+>(.*)<\/[^>]+>/', $value)) {
            return "<span class='dashed'>{$value}</span>";
        }

        return $value;
    }

    public function render($value)
    {
        $pageName = $this->column->grid()->model()->getPageName();

        $url = request()->fullUrlWithQuery([
            $this->queryName() => $this->originalValue(),
            $pageName          => null,
        ]);

        return "<a class='value-filter' href='{$url}'>{$this->wrap($value)}</a> &nbsp;<a style='opacity:0;' class='fa fa-search'></a>";
    }
}
