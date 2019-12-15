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
