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
    protected function href()
    {
    }

    /**
     * @return string|void
     */
    public function html()
    {
        if ($href = $this->href()) {
            $this->disabledHandler = true;
        }

        $this->setHtmlAttribute([
            'data-_key' => $this->key(),
            'href'      => $href ?: 'javascript:void(0);',
            'class'     => $this->style.' '.$this->elementClass(),
        ]);

        return "<a {$this->formatHtmlAttributes()}>{$this->title()}</a>";
    }
}
