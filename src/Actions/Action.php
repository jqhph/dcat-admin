<?php

namespace Dcat\Admin\Actions;

use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\HasHtmlAttributes;
use Illuminate\Contracts\Support\Renderable;

abstract class Action implements Renderable
{
    use HasHtmlAttributes, ActionHandler;

    /**
     * @var array
     */
    protected static $selectors = [];

    /**
     * @var array|string
     */
    protected $primaryKey;

    /**
     * @var string
     */
    protected $title;

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
     * @var bool
     */
    protected $disabled = false;

    /**
     * @var bool
     */
    protected $disabledHandler = false;

    /**
     * Action constructor.
     *
     * @param mixed  $key
     * @param string $title
     */
    public function __construct($key = null, $title = null)
    {
        $this->setKey($key);

        $this->title = $title;
    }

    /**
     * Toggle this action.
     *
     * @param bool $disable
     *
     * @return $this
     */
    public function disable(bool $disable = true)
    {
        $this->disabled = $disable;

        return $this;
    }

    /**
     * If the action is allowed.
     *
     * @return bool
     */
    public function allowed()
    {
        return ! $this->disabled;
    }

    /**
     * Get primary key value of action.
     *
     * @return array|string
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
     * Get action title.
     *
     * @return string
     */
    public function title()
    {
        return $this->title;
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

    /**
     * @return void
     */
    protected function prepareHandle()
    {
        if (
            $this->disabledHandler
            || ! method_exists($this, 'handle')
        ) {
            return;
        }

        $this->addHandlerScript();
    }

    /**
     * @return string
     */
    public function render()
    {
        if (! $this->allowed()) {
            return '';
        }

        $this->prepareHandle();
        $this->addScript();

        return $this->html();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return Helper::render($this->render());
    }

    /**
     * Create a action instance.
     *
     * @param mixed ...$params
     *
     * @return $this
     */
    public static function make(...$params)
    {
        return new static(...$params);
    }
}
