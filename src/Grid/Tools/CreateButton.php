<?php

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;

class CreateButton
{
    /**
     * @var Grid
     */
    protected $grid;

    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    public function render()
    {
        $new = trans('admin.new');
        $url = $this->grid->getCreateUrl();

        $quickBtn = $btn = '';
        if ($this->grid->option('show_create_btn')) {
            $btn = "<a href='{$url}' class='btn btn-sm btn-success btn-mini'>
    <i class='glyphicon glyphicon-plus-sign'></i><span class='hidden-xs'>&nbsp;&nbsp;{$new}</span>
</a>";
        }

        if ($this->grid->option('show_quick_create_btn')) {
            list($width, $height) = $this->grid->option('dialog_form_area');

            Form::popup($new)
                ->click(".{$this->grid->getGridRowName()}-create")
                ->success('LA.reload()')
                ->dimensions($width, $height)
                ->render();

            $text = $this->grid->option('show_create_btn') ? '' : "<span class='hidden-xs'> &nbsp; $new</span>";

            $quickBtn = "<a data-url='$url' class='btn btn-sm btn-success {$this->grid->getGridRowName()}-create'><i class=' fa fa-clone'></i>$text</a>";
        }

        return "<div class='btn-group' style='margin-right:3px'>{$btn}{$quickBtn}</div>";
    }
}
