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
$('.dropdown-menu input').on('click', function(e) {
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
&nbsp;<span class="dropdown">
    <form action="{$this->formAction()}" pjax-container style="display: inline-block;">
    <a href="javascript:void(0);" class="feather icon-filter {$active}" data-toggle="dropdown">
    </a>
    <ul class="dropdown-menu" role="menu" style="width: 250px;padding: 10px;left: -70px;border-radius: 0;font-weight:normal;background:#fff;">
        <li>
            <input placeholder="{$this->placeholder}" type="text" name="{$this->getQueryName()}" value="{$value}" class="form-control input-sm {$this->class}" autocomplete="off"/>
        </li>
        {$this->renderFormButtons()}
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
    public function valueFilter($valueKey = null)
    {
        return $this->resolving(function () use ($valueKey) {
            $valueFilter = new ValueFilter($this, $valueKey);

            return $this->parent()->display(function ($value) use ($valueFilter) {
                return $valueFilter->render($value);
            });
        });
    }
}
