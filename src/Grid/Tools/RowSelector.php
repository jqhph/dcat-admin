<?php

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Dcat\Admin\Widgets\Color;

class RowSelector
{
    protected $grid;

    protected $style = 'primary';

    protected $circle = true;

    protected $background;

    protected $rowClickable = false;

    protected $titleKey;

    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }

    public function style(string $style)
    {
        $this->style = $style;

        return $this;
    }

    public function circle(bool $value = true)
    {
        $this->circle = $value;

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

    public function titleKey(string $value)
    {
        $this->titleKey = $value;

        return $this;
    }

    public function renderHeader()
    {
        $circle = $this->circle ? 'checkbox-circle' : '';

        return <<<HTML
<div class="checkbox checkbox-{$this->style} {$circle} checkbox-grid">
    <input type="checkbox" class="select-all {$this->grid->selectAllName()}"><label></label>
</div>
HTML;
    }

    public function renderColumn($row, $id)
    {
        $this->setupScript();

        $circle = $this->circle ? 'checkbox-circle' : '';

        return <<<EOT
<div class="checkbox {$circle} checkbox-{$this->style} checkbox-grid">
    <input type="checkbox" class="{$this->grid->rowName()}-checkbox" data-id="{$id}" data-label="{$this->title($row, $id)}">
    <label></label>
</div>
EOT;
    }

    protected function setupScript()
    {
        $clickable = $this->rowClickable ? 'true' : 'false';
        $background = $this->background ?: Color::dark20();

        Admin::script(
            <<<JS
var selector = LA.RowSelector({
    checkbox: '.{$this->grid->rowName()}-checkbox',
    selectAll: '.{$this->grid->selectAllName()}', 
    clickTr: {$clickable},
    bg: '{$background}',
});
LA.grid.addSelector(selector, '{$this->grid->getName()}');
JS
        );
    }

    protected function title($row, $id)
    {
        if ($key = $this->titleKey) {
            $label = $row->{$key};
            if ($label !== null && $label !== '') {
                return $label;
            }

            return $id;
        }

        $label = $row->name ?: $row->title;

        return $label ?: ($row->username ?: $id);
    }
}
