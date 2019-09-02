<?php

namespace Dcat\Admin\Grid\Tools;

use Dcat\Admin\Grid;
use Illuminate\Contracts\Support\Renderable;

abstract class BatchAction implements Renderable
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $resource;

    /**
     * @var Grid
     */
    protected $grid;

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param Grid $grid
     */
    public function setGrid(Grid $grid)
    {
        $this->grid = $grid;

        $this->resource = $grid->getResource();
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return csrf_token();
    }

    /**
     * @param bool $dotPrefix
     *
     * @return string
     */
    public function getElementClass()
    {
        return sprintf(
            '%s-%s',
            $this->grid->getGridBatchName(),
            $this->id
        );
    }

    /**
     * @return string
     */
    public function getElementSelector()
    {
        return '.'.$this->getElementClass();
    }

    /**
     * Script of batch action.
     *
     * @return string
     */
    abstract public function script();

    public function render()
    {
        return <<<HTML
<li><a href="#" class="{$this->getElementClass()}">{$this->getTitle()}</a></li>
HTML;

    }
}
