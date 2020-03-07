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
    public function html()
    {
        $this->setHtmlAttribute([
            'data-_key' => $this->key(),
            'class'     => $this->style.' '.$this->elementClass(),
        ]);

        return parent::html();
    }
}
