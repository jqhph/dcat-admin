<?php

namespace Dcat\Admin\Grid;

use Illuminate\Support\Fluent;

abstract class RowAction extends GridAction
{
    /**
     * @var Fluent
     */
    protected $row;

    /**
     * @var Column
     */
    protected $column;

    /**
     * Get primary key value of current row.
     *
     * @return mixed
     */
    public function getKey()
    {
        if ($this->row) {
            return $this->row->{$this->parent->getKeyName()};
        }

        return parent::getKey();
    }

    /**
     * Set row model.
     *
     * @param  mixed  $key
     * @return \Illuminate\Database\Eloquent\Model|mixed
     */
    public function row($key = null)
    {
        if (func_num_args() == 0) {
            return $this->row;
        }

        return $this->row->{$key};
    }

    /**
     * Set row model.
     *
     * @param  Fluent|\Illuminate\Database\Eloquent\Model  $row
     * @return $this
     */
    public function setRow($row)
    {
        $this->row = $row;

        return $this;
    }

    public function getRow()
    {
        return $this->row;
    }

    /**
     * @param  Column  $column
     * @return $this
     */
    public function setColumn(Column $column)
    {
        $this->column = $column;

        return $this;
    }
}
