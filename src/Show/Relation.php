<?php

namespace Dcat\Admin\Show;

use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Illuminate\Support\Fluent;

class Relation extends Field
{
    /**
     * Relation name.
     *
     * @var string
     */
    protected $name;

    /**
     * Relation panel builder.
     *
     * @var \Closure
     */
    protected $builder;

    /**
     * Relation panel title.
     *
     * @var string
     */
    protected $title;

    /**
     * Parent model.
     *
     * @var Fluent
     */
    protected $model;

    /**
     * Relation constructor.
     *
     * @param string   $name
     * @param \Closure $builder
     * @param string   $title
     */
    public function __construct($name, $builder, $title = '')
    {
        $this->name = $name;
        $this->builder = $builder;
        $this->title = $this->formatLabel($title);
    }

    /**
     * Set parent model for relation.
     *
     * @param Fluent $model
     *
     * @return $this|Fluent
     */
    public function model(Fluent $model = null)
    {
        if ($model === null) {
            return $this->model;
        }

        $this->model = $model;

        return $this;
    }

    /**
     * Render this relation panel.
     *
     * @return string
     */
    public function render()
    {
        $view = call_user_func($this->builder, $this->model);

        if ($view instanceof Show) {
            return $this->renderTitle().$view->render();
        }

        if (! $view instanceof Grid) {
            return $this->renderTitle().$view;
        }

        $view->setName($this->name)
            ->disableFilterButton()
            ->disableBatchDelete()
            ->disableFilter();

        $filter = $view->filter()
            ->expand()
            ->withoutInputBorder()
            ->hiddenResetButtonText()
            ->expand()
            ->style('padding:0 0 5px;left:-5px;');

        $filter = "<div class='row'><div class='col-md-12'>{$filter->render()}</div></div>";

        return $this->renderTitle().$filter.$view->render();
    }

    /**
     * @return string
     */
    protected function renderTitle()
    {
        return <<<EOF
<div class="row">
     <div class="col-md-12" style="margin-bottom:.75rem;font-size:18px;text-transform:uppercase">
        <span class="show-relation-grid-title">{$this->title}</span>
     </div>
</div>
EOF;
    }
}
