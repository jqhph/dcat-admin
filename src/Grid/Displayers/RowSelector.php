<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;

class RowSelector extends AbstractDisplayer
{
    public function display()
    {
        $clickTr = $this->grid->option('row_selector_clicktr') ? 'true' : 'false';
        Admin::script(
            <<<JS
LA.RowSelector({
    checkbox: '.{$this->grid->getGridRowName()}-checkbox',
    selectAll: '.{$this->grid->getSelectAllName()}', 
    getSelectedRowsMethod: '{$this->grid->getSelectedRowsName()}',
    clickTr: {$clickTr},
    bg: '{$this->grid->option('row_selector_bg')}',
});
JS
        );

        $style  = $this->grid->option('row_selector_style');
        $circle = $this->grid->option('row_selector_circle') ? 'checkbox-circle' : '';

        return <<<EOT
<div class="checkbox $circle checkbox-$style checkbox-grid">
    <input type="checkbox" class="{$this->grid->getGridRowName()}-checkbox" data-id="{$this->getKey()}" data-label="{$this->getLabel()}">
    <label></label>
</div>
EOT;
    }

    /**
     * @return string
     */
    protected function getLabel()
    {
        if ($column = $this->grid->option('row_selector_label_name')) {
            $label = $this->row->{$column};
            if ($label !== null && $label !== '') {
                return $label;
            }

            return $this->getKey();
        }

        $label = $this->row->name ?: $this->row->title;

        return $label ?: ($this->row->username ?: $this->getKey());
    }

}
