<?php

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Dcat\Admin\Widgets\Checkbox;
use Illuminate\Support\Collection;

class ColumnSelector extends AbstractTool
{
    const SELECT_COLUMN_NAME = '_columns_';

    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @var array
     */
    protected static $ignoredColumns = [
        Grid\Column::SELECT_COLUMN_NAME,
        Grid\Column::ACTION_COLUMN_NAME,
    ];

    /**
     * Create a new Export button instance.
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function render()
    {
        if (! $this->grid->allowColumnSelector()) {
            return '';
        }

        $show = $this->grid->getVisibleColumnNames();

        $list = new Checkbox();

        $list->class('column-select-item');
        $list->options($this->getGridColumns());
        $list->check(
            $this->getGridColumns()->filter(function ($label, $key) use ($show) {
                if (empty($show)) {
                    return true;
                }

                return in_array($key, $show) ? true : false;
            })->keys()
        );

        return Admin::view('admin::grid.column-selector', [
            'checkbox' => $list,
            'defaults' => $this->grid->getDefaultVisibleColumnNames(),
        ]);
    }

    /**
     * @return Collection
     */
    protected function getGridColumns()
    {
        return $this->grid->columns()->map(function (Grid\Column $column) {
            $name = $column->getName();

            if ($this->isColumnIgnored($name)) {
                return;
            }

            return [$name => $column->getLabel()];
        })->filter()->collapse();
    }

    /**
     * Is column ignored in column selector.
     *
     * @param string $name
     *
     * @return bool
     */
    protected function isColumnIgnored($name)
    {
        return in_array($name, static::$ignoredColumns);
    }

    /**
     * Ignore a column to display in column selector.
     *
     * @param string|array $name
     */
    public static function ignore($name)
    {
        static::$ignoredColumns = array_merge(static::$ignoredColumns, (array) $name);
    }
}
