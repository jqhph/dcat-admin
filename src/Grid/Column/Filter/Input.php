<?php

namespace Dcat\Admin\Grid\Column\Filter;

use Dcat\Admin\Admin;

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
     * @return $this
     */
    public function placeholder(?string $placeholder)
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    protected function renderInput()
    {
        $this->addScript();

        $value = $this->getFilterValue();

        $active = empty($value) ? '' : 'text-yellow';

        return <<<EOT
&nbsp;<span class="dropdown" style="position: absolute">
    <form action="{$this->getFormAction()}" pjax-container style="display: inline-block;">
    <a href="javascript:void(0);" class="dropdown-toggle {$active}" data-toggle="dropdown">
        <i class="fa fa-filter"></i>
    </a>
    <ul class="dropdown-menu" role="menu" style="padding: 10px;box-shadow: 0 2px 3px 0 rgba(0,0,0,.2);left: -70px;border-radius: 0;font-weight:normal;background:#fff;">
        <li>
            <input placeholder="{$this->placeholder}" type="text" name="{$this->getFormName()}" value="{$this->getFilterValue()}" class="form-control input-sm {$this->class}" autocomplete="off"/>
        </li>
        <li class="divider"></li>
        <li class="">
            <button class="btn btn-sm btn-primary column-filter-submit "><i class="fa fa-search"></i></button>
            <span onclick="LA.reload('{$this->urlWithoutFilter()}')" class="btn btn-sm btn-default column-filter-all"><i class="fa fa-undo"></i></span>
        </li>
    </ul>
    </form>
</span>
EOT;
    }
}
