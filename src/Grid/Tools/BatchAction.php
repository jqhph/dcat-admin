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
    public function id($id = null)
    {
        if ($id === null) {
            return $this->id;
        }

        $this->id = $id;
    }

    public function title($title = null)
    {
        if ($title === null) {
            return $this->title;
        }

        $this->title = $title;
    }

    /**
     * @param Grid $grid
     */
    public function setGrid(Grid $grid)
    {
        $this->grid = $grid;

        $this->resource = $grid->resource();
    }

    /**
     * @return string
     */
    public function token()
    {
        return csrf_token();
    }

    /**
     * @param bool $dotPrefix
     *
     * @return string
     */
    public function elementClass()
    {
        return sprintf(
            '%s-%s',
            $this->grid->batchName(),
            $this->id
        );
    }

    /**
     * @return string
     */
    public function elementSelector()
    {
        return '.'.$this->elementClass();
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
<li><a href="#" class="{$this->elementClass()}">{$this->title()}</a></li>
HTML;
    }
}
