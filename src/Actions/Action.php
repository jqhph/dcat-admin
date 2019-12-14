<?php

namespace Dcat\Admin\Actions;

use Dcat\Admin\Traits\HasHtmlAttributes;
use Illuminate\Contracts\Support\Renderable;

abstract class Action implements Renderable
{
    use HasHtmlAttributes,
        Authorizable,
        ActionHandler;

    /**
     * @var array
     */
    protected static $selectors = [];

    /**
     * @var mixed
     */
    protected $primaryKey;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $selector;

    /**
     * @var string
     */
    public $selectorPrefix = '.admin-action-';

    /**
     * @var string
     */
    protected $method = 'POST';

    /**
     * @var string
     */
    protected $event = 'click';

    /**
     * Get primary key value of action.
     *
     * @return mixed
     */
    public function key()
    {
        return $this->primaryKey;
    }

    /**
     * Set primary key value of action.
     *
     * @param mixed $key
     *
     * @return void
     */
    public function setKey($key)
    {
        $this->primaryKey = $key;
    }

    /**
     * @return string
     */
    protected function elementClass()
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
        if (! isset(static::$selectors[$class])) {
            static::$selectors[$class] = uniqid($prefix);
        }

        return static::$selectors[$class];
    }

    /**
     * @return void
     */
    protected function addScript()
    {
    }

    /**
     * @return string
     */
    protected function html()
    {
    }

    protected function prepareHandle()
    {
        if (! method_exists($this, 'handle')) {
            return;
        }

        $this->addHandlerScript();
    }

    /**
     * @return mixed
     */
    public function render()
    {
        $this->prepareHandle();
        $this->addScript();

        return $this->html();
    }
}
