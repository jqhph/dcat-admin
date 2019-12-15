<?php

namespace Dcat\Admin\Tree;

use Dcat\Admin\Actions\Action;
use Dcat\Admin\Tree;

abstract class AbstractTool extends Action
{
    /**
     * @var Tree
     */
    protected $parent;

    /**
     * @var string
     */
    public $selectorPrefix = '.tree-tool-action-';

    /**
     * @var string
     */
    protected $style = 'btn btn-sm btn-primary';

    /**
     * @param Tree $parent
     *
     * @return void
     */
    public function setParent(Tree $parent)
    {
        $this->parent = $parent;
    }

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
