<?php

namespace Dcat\Admin\Form;

use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;

class Tools implements Renderable
{
    /**
     * @var Builder
     */
    protected $form;

    /**
     * Collection of tools.
     *
     * @var array
     */
    protected $tools = ['delete' => true, 'view' => true, 'list' => true];

    /**
     * Tools should be appends to default tools.
     *
     * @var Collection
     */
    protected $appends;

    /**
     * Tools should be prepends to default tools.
     *
     * @var Collection
     */
    protected $prepends;

    /**
     * Create a new Tools instance.
     *
     * @param  Builder  $builder
     */
    public function __construct(Builder $builder)
    {
        $this->form = $builder;
        $this->appends = new Collection();
        $this->prepends = new Collection();
    }

    /**
     * Append a tools.
     *
     * @param  string|\Closure|Renderable|Htmlable|AbstractTool  $tool
     * @return $this
     */
    public function append($tool)
    {
        $this->prepareTool($tool);

        $this->appends->push($tool);

        return $this;
    }

    /**
     * Prepend a tool.
     *
     * @param  string|\Closure|Renderable|Htmlable|AbstractTool  $tool
     * @return $this
     */
    public function prepend($tool)
    {
        $this->prepareTool($tool);

        $this->prepends->push($tool);

        return $this;
    }

    /**
     * @param  mixed  $tool
     * @return void
     */
    protected function prepareTool($tool)
    {
        if ($tool instanceof AbstractTool) {
            $tool->setForm($this->form->form());
        }
    }

    /**
     * Disable `list` tool.
     *
     * @return $this
     */
    public function disableList(bool $disable = true)
    {
        $this->tools['list'] = ! $disable;

        return $this;
    }

    /**
     * Disable `delete` tool.
     *
     * @return $this
     */
    public function disableDelete(bool $disable = true)
    {
        $this->tools['delete'] = ! $disable;

        return $this;
    }

    /**
     * Disable `view` tool.
     *
     * @return $this
     */
    public function disableView(bool $disable = true)
    {
        $this->tools['view'] = ! $disable;

        return $this;
    }

    /**
     * Get request path for resource list.
     *
     * @return string
     */
    protected function getListPath()
    {
        return $this->form->resource();
    }

    /**
     * Get request path for edit.
     *
     * @return string
     */
    protected function getDeletePath()
    {
        return $this->getViewPath();
    }

    /**
     * Get request path for delete.
     *
     * @return string
     */
    protected function getViewPath()
    {
        if ($key = $this->form->getResourceId()) {
            return $this->getListPath().'/'.$key;
        }

        return $this->getListPath();
    }

    /**
     * Get parent form of tool.
     *
     * @return Builder
     */
    public function form()
    {
        return $this->form;
    }

    /**
     * Render list button.
     *
     * @return string
     */
    protected function renderList()
    {
        $text = trans('admin.list');

        return <<<EOT
<div class="btn-group pull-right" style="margin-right: 5px">
    <a href="{$this->getListPath()}" class="btn btn-sm btn-primary "><i class="feather icon-list"></i><span class="d-none d-sm-inline">&nbsp;$text</span></a>
</div>
EOT;
    }

    /**
     * Render list button.
     *
     * @return string
     */
    protected function renderView()
    {
        $view = trans('admin.view');

        return <<<HTML
<div class="btn-group pull-right" style="margin-right: 5px">
    <a href="{$this->getViewPath()}" class="btn btn-sm btn-primary">
        <i class="feather icon-eye"></i><span class="d-none d-sm-inline"> {$view}</span>
    </a>
</div>
HTML;
    }

    /**
     * Render `delete` tool.
     *
     * @return string
     */
    protected function renderDelete()
    {
        $delete = trans('admin.delete');

        return <<<HTML
<div class="btn-group pull-right" style="margin-right: 5px">
    <a class="btn btn-sm btn-white" data-action="delete" data-url="{$this->getDeletePath()}" data-redirect="{$this->getListPath()}">
        <i class="feather icon-trash"></i><span class="d-none d-sm-inline"> {$delete}</span>
    </a>
</div>
HTML;
    }

    /**
     * Render custom tools.
     *
     * @param  Collection  $tools
     * @return mixed
     */
    protected function renderCustomTools($tools)
    {
        if ($this->form->isCreating()) {
            $this->disableView();
            $this->disableDelete();
        }

        if (empty($tools)) {
            return '';
        }

        return $tools->map([Helper::class, 'render'])->implode(' ');
    }

    /**
     * Render tools.
     *
     * @return string
     */
    public function render()
    {
        $output = $this->renderCustomTools($this->prepends);

        foreach ($this->tools as $tool => $enable) {
            if ($enable) {
                $renderMethod = 'render'.ucfirst($tool);

                $output .= $this->$renderMethod();
            }
        }

        return $output.$this->renderCustomTools($this->appends);
    }
}
