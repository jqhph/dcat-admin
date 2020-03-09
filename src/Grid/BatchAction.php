<?php

namespace Dcat\Admin\Grid;

abstract class BatchAction extends GridAction
{
    /**
     * @var string
     */
    public $selectorPrefix = '.grid-batch-action-';

    /**
     * {@inheritdoc}
     */
    public function actionScript()
    {
        $warning = __('No data selected!');

        return <<<JS
    var key = {$this->getSelectedKeysScript()}
    
    if (key.length === 0) {
        LA.warning('{$warning}');
        return ;
    }
    Object.assign(data, {_key:key});
JS;
    }

    /**
     * @return string
     */
    public function getSelectedKeysScript()
    {
        return "LA.grid.selected('{$this->parent->getName()}');";
    }
}
