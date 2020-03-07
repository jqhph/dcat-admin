<?php

namespace Dcat\Admin\Show;

use Dcat\Admin\Actions\Action;
use Dcat\Admin\Show;

abstract class AbstractTool extends Action
{
    /**
     * @var Show
     */
    protected $parent;

    /**
     * @var string
     */
    public $selectorPrefix = '.show-tool-action-';

    /**
     * @var string
     */
    protected $style = 'btn btn-sm btn-primary';

    /**
     * @param Show $show
     *
     * @return void
     */
    public function setParent(Show $show)
    {
        $this->parent = $show;
    }

    /**
     * @return array|mixed|string|null
     */
    public function key()
    {
        if ($this->primaryKey) {
            return $this->primaryKey;
        }

        return $this->parent ? $this->parent->key() : null;
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
