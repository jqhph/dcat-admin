<?php

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Grid;

abstract class AbstractTool extends Grid\GridAction
{
    /**
     * @var string
     */
    public $selectorPrefix = '.tool-action-';

    /**
     * @var string
     */
    protected $style = 'btn btn-sm btn-primary';

    /**
     * @return void
     */
    public function setupHtmlAttributes()
    {
        $this->addHtmlClass($this->style);

        parent::setupHtmlAttributes();
    }
}
