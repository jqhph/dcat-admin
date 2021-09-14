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
    protected $style = 'btn btn-sm btn-primary';

    /**
     * @param  Tree  $parent
     * @return void
     */
    public function setParent(Tree $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return void
     */
    public function setupHtmlAttributes()
    {
        $this->addHtmlClass($this->style);

        parent::setupHtmlAttributes();
    }
}
