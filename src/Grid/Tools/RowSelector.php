<?php

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Illuminate\Support\Arr;

class RowSelector
{
    protected $grid;

    protected $style = 'primary';

    protected $background;

    protected $rowClickable = false;

    protected $idColumn;

    protected $titleColumn;

    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    public function style(string $style)
    {
        $this->style = $style;

        return $this;
    }

    public function background(string $value)
    {
        $this->background = $value;

        return $this;
    }

    public function click(bool $value = true)
    {
        $this->rowClickable = $value;

        return $this;
    }

    public function idColumn(string $value)
    {
        $this->idColumn = $value;

        return $this;
    }

    public function titleColumn(string $value)
    {
        $this->titleColumn = $value;

        return $this;
    }

    public function renderHeader()
    {
        return <<<HTML
<div class="vs-checkbox-con vs-checkbox-{$this->style} checkbox-grid checkbox-grid-header">
    <input type="checkbox" class="select-all {$this->grid->getSelectAllName()}">
    <span class="vs-checkbox"><span class="vs-checkbox--check"><i class="vs-icon feather icon-check"></i></span></span>
</div>
HTML;
    }

    public function renderColumn($row, $id)
    {
        $this->addScript();
        $title = $this->getTitle($row, $id);
        $title = e(is_array($title) ? json_encode($title) : $title);
        $id = $this->idColumn ? Arr::get($row->toArray(), $this->idColumn) : $id;

        return <<<EOT
<div class="vs-checkbox-con vs-checkbox-{$this->style} checkbox-grid checkbox-grid-column">
    <input type="checkbox" class="{$this->grid->getRowName()}-checkbox" data-id="{$id}" data-label="{$title}">
    <span class="vs-checkbox"><span class="vs-checkbox--check"><i class="vs-icon feather icon-check"></i></span></span>
</div>        
EOT;
    }

    protected function addScript()
    {
        $clickable = $this->rowClickable ? 'true' : 'false';
        $background = $this->background ?: Admin::color()->dark20();

        Admin::script(
            <<<JS
var selector = Dcat.RowSelector({
    checkboxSelector: '.{$this->grid->getRowName()}-checkbox',
    selectAllSelector: '.{$this->grid->getSelectAllName()}', 
    clickRow: {$clickable},
    background: '{$background}',
});
Dcat.grid.addSelector(selector, '{$this->grid->getName()}');
JS
        );
    }

    protected function getTitle($row, $id)
    {
        if ($key = $this->titleColumn) {
            $label = Arr::get($row->toArray(), $key);
            if ($label !== null && $label !== '') {
                return $label;
            }

            return $id;
        }

        $label = $row->name ?: $row->title;

        return $label ?: ($row->username ?: $id);
    }
}
