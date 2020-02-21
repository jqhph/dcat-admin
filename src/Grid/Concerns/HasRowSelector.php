<?php

namespace Dcat\Admin\Grid\Concerns;

use Dcat\Admin\Grid;

/**
 * @mixin Grid
 */
trait HasRowSelector
{
    /**
     * @var Grid\Tools\RowSelector
     */
    protected $_rowSelector;

    /**
     * @param \Closure $closure
     *
     * @return Grid\Tools\RowSelector
     */
    public function rowSelector()
    {
        return $this->_rowSelector ?: ($this->_rowSelector = new Grid\Tools\RowSelector($this));
    }

    /**
     * Prepend checkbox column for grid.
     *
     * @return void
     */
    protected function prependRowSelectorColumn()
    {
        if (! $this->options['show_row_selector']) {
            return;
        }

        $rowSelector = $this->rowSelector();
        $keyName = $this->keyName();

        $column = new Grid\Column(
            Grid\Column::SELECT_COLUMN_NAME,
            $rowSelector->renderHeader()
        );
        $column->setGrid($this);

        $column->display(function () use ($rowSelector, $keyName)  {
            return $rowSelector->render($this, $this->{$keyName});
        });

        $this->columns->prepend($column, Grid\Column::SELECT_COLUMN_NAME);
    }
}
