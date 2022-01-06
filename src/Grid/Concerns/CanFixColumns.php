<?php

namespace Dcat\Admin\Grid\Concerns;

use Dcat\Admin\Grid\Displayers\Actions;
use Dcat\Admin\Grid\Displayers\DropdownActions;
use Dcat\Admin\Grid\FixColumns;
use Illuminate\Support\Collection;

trait CanFixColumns
{
    /**
     * @var FixColumns
     */
    protected $fixColumns;

    /**
     * @param  int  $head
     * @param  int  $tail
     * @return FixColumns
     */
    public function fixColumns(int $head, int $tail = -1)
    {
        $this->fixColumns = new FixColumns($this, $head, $tail);

        $this->resetActions();

        return $this->fixColumns;
    }

    public function hasFixColumns()
    {
        return $this->fixColumns;
    }

    protected function resetActions()
    {
        $actions = $this->getActionClass();

        if ($actions === DropdownActions::class) {
            $this->setActionClass(Actions::class);
        }
    }

    protected function applyFixColumns()
    {
        if ($this->fixColumns) {
            if (! $this->options['bordered'] && ! $this->options['table_collapse']) {
                $this->tableCollapse();
            }

            $this->fixColumns->apply();
        }
    }

    /**
     * @return Collection
     */
    public function leftVisibleColumns()
    {
        return $this->fixColumns->leftColumns();
    }

    /**
     * @return Collection
     */
    public function rightVisibleColumns()
    {
        return $this->fixColumns->rightColumns();
    }

    /**
     * @return Collection
     */
    public function leftVisibleComplexColumns()
    {
        return $this->fixColumns->leftComplexColumns();
    }

    /**
     * @return Collection
     */
    public function rightVisibleComplexColumns()
    {
        return $this->fixColumns->rightComplexColumns();
    }
}
