<?php

namespace Dcat\Admin\Grid;

use Dcat\Admin\Actions\Action;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Tools\AbstractTool;
use Dcat\Admin\Grid\Tools\BatchActions;
use Dcat\Admin\Grid\Tools\FilterButton;
use Dcat\Admin\Grid\Tools\RefreshButton;
use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;

class Tools implements Renderable
{
    /**
     * Parent grid.
     *
     * @var Grid
     */
    protected $grid;

    /**
     * Collection of tools.
     *
     * @var Collection
     */
    protected $tools;

    /**
     * Create a new Tools instance.
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;

        $this->tools = new Collection();

        $this->appendDefaultTools();
    }

    /**
     * Append default tools.
     */
    protected function appendDefaultTools()
    {
        $this->append(new BatchActions())
            ->append(new RefreshButton())
            ->append(new FilterButton());
    }

    /**
     * Append tools.
     *
     * @param AbstractTool|string|\Closure|Renderable|Htmlable $tool
     *
     * @return $this
     */
    public function append($tool)
    {
        $this->prepareAction($tool);

        $this->tools->push($tool);

        return $this;
    }

    /**
     * Prepend a tool.
     *
     * @param AbstractTool|string|\Closure|Renderable|Htmlable $tool
     *
     * @return $this
     */
    public function prepend($tool)
    {
        $this->prepareAction($tool);

        $this->tools->prepend($tool);

        return $this;
    }

    /**
     * @param mixed $tool
     *
     * @return void
     */
    protected function prepareAction($tool)
    {
        if ($tool instanceof GridAction) {
            $tool->setGrid($this->grid);
        }
    }

    /**
     * @return bool
     */
    public function has()
    {
        return ! $this->tools->isEmpty();
    }

    /**
     * Disable filter button.
     *
     * @return void
     */
    public function disableFilterButton(bool $disable = true)
    {
        $this->tools = $this->tools->map(function ($tool) use ($disable) {
            if ($tool instanceof FilterButton) {
                return $tool->disable($disable);
            }

            return $tool;
        });
    }

    /**
     * Disable refresh button.
     *
     * @return void
     */
    public function disableRefreshButton(bool $disable = true)
    {
        $this->tools = $this->tools->map(function ($tool) use ($disable) {
            if ($tool instanceof RefreshButton) {
                return $tool->disable($disable);
            }

            return $tool;
        });
    }

    /**
     * Disable batch actions.
     *
     * @return void
     */
    public function disableBatchActions(bool $disable = true)
    {
        $this->tools = $this->tools->map(function ($tool) use ($disable) {
            if ($tool instanceof BatchActions) {
                return $tool->disable($disable);
            }

            return $tool;
        });
    }

    /**
     * @param \Closure|BatchAction|BatchAction[] $value
     */
    public function batch($value)
    {
        /* @var BatchActions $batchActions */
        $batchActions = $this->tools->first(function ($tool) {
            return $tool instanceof BatchActions;
        });

        if ($value instanceof \Closure) {
            $value($batchActions);

            return;
        }

        if (! is_array($value)) {
            $value = [$value];
        }

        foreach ($value as $action) {
            $batchActions->add($action);
        }
    }

    /**
     * Render header tools bar.
     *
     * @return string
     */
    public function render()
    {
        return $this->tools->map(function ($tool) {
            if ($tool instanceof Action && ! $tool->allowed()) {
                return;
            }

            return Helper::render($tool);
        })->implode(' ');
    }
}
