<?php

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Illuminate\Contracts\Support\Renderable;

class CreateButton implements Renderable
{
    /**
     * @var Grid
     */
    protected $grid;

    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    protected function renderQuickCreateButton()
    {
        if (! $this->grid->option('show_quick_create_button')) {
            return;
        }

        $new = trans('admin.new');
        $url = $this->grid->createUrl();

        [$width, $height] = $this->grid->option('dialog_form_area');

        Form::modal($new)
            ->click(".{$this->grid->rowName()}-create")
            ->success('LA.reload()')
            ->dimensions($width, $height)
            ->render();

        $text = $this->grid->option('show_create_button') ? '<i class="fa fa-clone"></i>' : "<i class='ti-plus'></i><span class='hidden-xs'> &nbsp; $new</span>";

        return "<a data-url='$url' class='btn btn-sm btn-success {$this->grid->rowName()}-create'>$text</a>";
    }

    protected function renderCreateButton()
    {
        if (! $this->grid->option('show_create_button')) {
            return;
        }

        $new = trans('admin.new');
        $url = $this->grid->createUrl();

        return "<a href='{$url}' class='btn btn-sm btn-success btn-mini'>
    <i class='ti-plus'></i><span class='hidden-xs'>&nbsp;&nbsp;{$new}</span>
</a>";
    }

    public function render()
    {
        if (! $this->grid->option('show_create_button') && ! $this->grid->option('show_quick_create_button')) {
            return;
        }

        return "<div class='btn-group' style='margin-right:3px'>{$this->renderCreateButton()}{$this->renderQuickCreateButton()}</div>";
    }
}
