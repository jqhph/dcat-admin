<?php

namespace Dcat\Admin\Grid;

abstract class BatchAction extends GridAction
{
    /**
     * {@inheritdoc}
     */
    protected function actionScript()
    {
        $warning = __('No data selected!');

        return <<<JS
function (data, target, action) { 
    var key = {$this->getSelectedKeysScript()}
    
    if (key.length === 0) {
        Dcat.warning('{$warning}');
        return false;
    }
    
    // 设置主键为复选框选中的行ID数组
    action.options.key = key;
}
JS;
    }

    /**
     * @return string
     */
    public function getSelectedKeysScript()
    {
        return "Dcat.grid.selected('{$this->parent->getName()}')";
    }
}
