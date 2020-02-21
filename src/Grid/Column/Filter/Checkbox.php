<?php

namespace Dcat\Admin\Grid\Column\Filter;

use Dcat\Admin\Admin;
use Illuminate\Support\Str;

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

        $allId = 'filter-all-'.Str::random(5);

        return <<<HTML
&nbsp;<span class="dropdown" style="position:absolute;">
<form action="{$this->formAction()}" pjax-container style="display: inline-block;">
    <a href="javascript:void(0);" class="dropdown-toggle {$active}" data-toggle="dropdown">
        <i class="fa fa-filter"></i>
    </a>
    <ul class="dropdown-menu" role="menu" style="padding: 10px;box-shadow: 0 2px 3px 0 rgba(0,0,0,.2);left: -70px;border-radius: 0;font-weight:normal;background:#fff">
        
        <li>
            <ul style='padding: 0;'>
            <li style="margin: 0;padding-left:5px">
                <div class="checkbox checkbox-primary checkbox-inline ">
                    <input class="{$this->class['all']}" id="{$allId}" type="checkbox" {$allCheck} />
                    <label for="{$allId}">&nbsp;{$this->trans('all')}</label>
                </div>
            </li>
                <li class="divider"></li>
                {$this->renderOptions($value)}
            </ul>
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

    protected function renderOptions($value)
    {
        return collect($this->options)->map(function ($label, $key) use ($value) {
            $checked = in_array($key, $value) ? 'checked' : '';

            $id = 'filter-ckb-'.Str::random(5);

            return <<<HTML
<li style="margin: 0;padding-left:5px">
    <div class="checkbox checkbox-primary checkbox-inline ">
        <input id="$id" type="checkbox" class="{$this->class['item']}" name="{$this->queryName()}[]" value="{$key}" {$checked}/>
        <label for="$id">&nbsp;{$label}</label>
    </div>
</li>
HTML;
        })->implode("\r\n");
    }
}
