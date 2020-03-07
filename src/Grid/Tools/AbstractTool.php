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
     * @return string|void
     */
    public function html()
    {
        $this->setHtmlAttribute([
            'data-_key' => $this->key(),
            'class'     => $this->style.' '.$this->elementClass(),
        ]);

        return parent::html();
    }
}
