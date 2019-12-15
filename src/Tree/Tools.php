<?php

namespace Dcat\Admin\Tree;

use Dcat\Admin\Support\Helper;
use Dcat\Admin\Tree;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;

class Tools implements Renderable
{
    /**
     * Parent tree.
     *
     * @var Tree
     */
    protected $tree;

    /**
     * Collection of tools.
     *
     * @var Collection
     */
    protected $tools;

    /**
     * Create a new Tools instance.
     */
    public function __construct(Tree $tree)
    {
        $this->tree = $tree;
        $this->tools = new Collection();
    }

    /**
     * Prepend a tool.
     *
     * @param string|\Closure|AbstractTool|Renderable|Htmlable $tool
     *
     * @return $this
     */
    public function add($tool)
    {
        if ($tool instanceof AbstractTool) {
            $tool->setParent($this->tree);
        }

        $this->tools->push($tool);

        return $this;
    }

    /**
     * Render header tools bar.
     *
     * @return string
     */
    public function render()
    {
        return $this->tools->map([Helper::class, 'render'])->implode(' ');
    }
}
