<?php

namespace Dcat\Admin\Grid\Column\Filter;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid\Column\ValueFilter;

trait Input
{
    protected $placeholder = null;

    /**
     * Add script.
     *
     * @return void
     */
    protected function addScript()
    {
        $script = <<<'JS'
$('.dropdown-menu input').click(function(e) {
    e.stopPropagation();
});
JS;
        Admin::script($script);
    }

    /**
     * Set input placeholder.
     *
     * @param null|string $placeholder
     *
     * @return $this
     */
    public function placeholder(?string $placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    protected function renderInput()
    {
        if (! $this->shouldDisplay()) {
            return;
        }

        $this->addScript();

        $value = $this->value();

        $active = empty($value) ? '' : 'active';

        return <<<HTML
&nbsp;<span class="dropdown" style="position: absolute">
    <form action="{$this->formAction()}" pjax-container style="display: inline-block;">
    <a href="javascript:void(0);" class="dropdown-toggle fa fa-filter {$active}" data-toggle="dropdown">
    </a>
    <ul class="dropdown-menu" role="menu" style="padding: 10px;box-shadow: 0 2px 3px 0 rgba(0,0,0,.2);left: -70px;border-radius: 0;font-weight:normal;background:#fff;">
        <li>
            <input placeholder="{$this->placeholder}" type="text" name="{$this->queryName()}" value="{$value}" class="form-control input-sm {$this->class}" autocomplete="off"/>
        </li>
        <li class="divider"></li>
        <li class="">
            <button class="btn btn-sm btn-primary column-filter-submit "><i class="fa fa-search"></i></button>
        </li>
    </ul>
    </form>
</span>
HTML;
    }


    /**
     * @param string|\Closure $valueKey
     *
     * @return $this
     */
    public function valueAsFilter($valueKey = null)
    {
        return $this->resolving(function () use ($valueKey) {
            $valueFilter = new ValueFilter($this, $valueKey);

            return $this->parent()->display(function ($value) use ($valueFilter) {
                return $valueFilter->render($value);
            });
        });
    }
}
