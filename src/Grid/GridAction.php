<?php

namespace Dcat\Admin\Grid;

use Dcat\Admin\Grid;
use Dcat\Admin\Traits\HasHtmlAttributes;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

/**
 * Class GridAction.
 *
 */
abstract class GridAction implements Renderable
{
    use HasHtmlAttributes;

    /**
     * @var array
     */
    protected static $selectors = [];

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $selector;

    /**
     * @var Grid
     */
    protected $parent;

    /**
     * @var string
     */
    public $selectorPrefix = '.grid-action-';

    /**
     * @param Grid $grid
     *
     * @return $this
     */
    public function setGrid(Grid $grid)
    {
        $this->parent = $grid;

        return $this;
    }

    /**
     * Get url path of current resource.
     *
     * @return string
     */
    public function getResource()
    {
        return $this->parent->getResource();
    }

    /**
     * @return string
     */
    protected function getElementClass()
    {
        return ltrim($this->selector($this->selectorPrefix), '.');
    }

    /**
     * Get batch action title.
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @param string $prefix
     *
     * @return mixed|string
     */
    public function selector($prefix)
    {
        if (is_null($this->selector)) {
            return static::makeSelector(get_called_class(), $prefix);
        }

        return $this->selector;
    }

    /**
     * @param string $class
     * @param string $prefix
     *
     * @return string
     */
    public static function makeSelector($class, $prefix)
    {
        if (!isset(static::$selectors[$class])) {
            static::$selectors[$class] = uniqid($prefix);
        }

        return static::$selectors[$class];
    }

    protected function addScript()
    {

    }

}
