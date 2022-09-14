<?php

namespace Dcat\Admin\Grid\Column\Filter;

use Dcat\Admin\Admin;

trait Checkbox
{
    /**
     * Add script to page.
     *
     * @return void
     */
    protected function addScript()
    {
        $script = <<<JS
$('.{$this->class['all']}').on('change', function () {
    if (this.checked) {
        $('.{$this->class['item']}').prop('checked', true);
    } else {
        $('.{$this->class['item']}').prop('checked', false);
    }
    return false;
});
JS;

        Admin::script($script);
    }

    protected function renderCheckbox()
    {
        if (! $this->shouldDisplay()) {
            return;
        }

        $value = $this->value([]);

        $this->addScript();

        $allCheck = (count($value) == count($this->options)) ? 'checked' : '';
        $active = empty($value) ? '' : 'active';
        $pjaxContainer = Admin::getPjaxContainerId();

        return <<<HTML
&nbsp;<span class="dropdown">
<form action="{$this->formAction()}" {$pjaxContainer} style="display: inline-block;">
    <a href="javascript:void(0);" class="{$active}" data-toggle="dropdown">
        <i class="feather icon-filter"></i>
    </a>
    <ul class="dropdown-menu" role="menu" style="padding: 10px;left: -70px;border-radius: 0;font-weight:normal;background:#fff">
        
        <li>
            <ul style='padding: 0;'>
                <li class="dropdown-item" style="margin: 0;padding-left:5px">
                    <div class="vs-checkbox-con vs-checkbox-primary checkbox-grid">
                        <input type="checkbox" class="{$this->class['all']}" {$allCheck}>
                        <span class="vs-checkbox"><span class="vs-checkbox--check"><i class="vs-icon feather icon-check"></i></span></span>
                         <span>{$this->trans('all')}</span>
                    </div>
                </li>
                <li class="dropdown-divider"></li>
                {$this->renderOptions($value)}
            </ul>
        </li>
        {$this->renderFormButtons()}
    </ul>
</form>
</span>
HTML;
    }

    protected function renderOptions($value)
    {
        return collect($this->options)->map(function ($label, $key) use ($value) {
            $checked = in_array($key, $value) ? 'checked' : '';

            return <<<HTML
<li style="margin: 0;padding:4px 0 4px 5px">
    <div class="vs-checkbox-con vs-checkbox-primary checkbox-grid">
        <input type="checkbox" class="{$this->class['item']}" {$checked} name="{$this->getQueryName()}[]" value="{$key}">
        <span class="vs-checkbox"><span class="vs-checkbox--check"><i class="vs-icon feather icon-check"></i></span></span>
         <span>{$label}</span>
    </div>
</li>
HTML;
        })->implode("\r\n");
    }
}
