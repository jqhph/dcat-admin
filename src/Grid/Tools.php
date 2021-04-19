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
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

class Tools implements Renderable
{
    use Macroable;

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
     * @var bool
     */
    protected $outline = true;

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
        $this->append($this->makeBatchActions())
            ->append(new RefreshButton())
            ->append(new FilterButton());
    }

    protected function makeBatchActions()
    {
        $class = $this->grid->option('batch_actions_class') ?: (config('admin.grid.batch_action_class') ?: BatchActions::class);

        return new $class();
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
     * @param bool $value
     *
     * @return $this
     */
    public function withOutline(bool $value)
    {
        $this->outline = $value;

        return $this;
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
                return $tool->display(! $disable);
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
        $value = $this->tools->map(function ($tool) {
            if ($tool instanceof Action && ! $tool->allowed()) {
                return;
            }

            return Helper::render($tool);
        })->implode(' ');

        return $this->addButtonOutline($value);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function format(string $value)
    {
        return $this->addButtonOutline($value);
    }

    /**
     * @param string $value
     *
     * @return string
     */
    protected function addButtonOutline($value)
    {
        if (! $this->outline) {
            return $value;
        }

        return preg_replace_callback('/class=[\'|"]([a-z0-9A-Z-_\s]*)[\'|"]/', function ($text) {
            $class = array_filter(explode(' ', $text[1]));

            if (
                in_array('btn', $class, true)
                && ! in_array('disable-outline', $class, true)
                && Str::contains($text[1], 'btn-')
            ) {
                $class[] = 'btn-outline';
            }

            return sprintf('class="%s"', implode(' ', $class));
        }, $value);
    }
}
