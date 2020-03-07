<?php

namespace Dcat\Admin\Actions;

use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\HasHtmlAttributes;
use Illuminate\Contracts\Support\Renderable;

/**
 * Class Action
 *
 * @method string href
 */
abstract class Action implements Renderable
{
    use HasHtmlAttributes, HasActionHandler;

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
    protected $usingHandler = true;

    /**
     * @var array
     */
    protected $htmlClasses = [];

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
        return ltrim($this->selector(), '.');
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
     * @return mixed|string
     */
    public function selector()
    {
        if (is_null($this->selector)) {
            return static::makeSelector($this->selectorPrefix);
        }

        return $this->selector;
    }

    /**
     * @param string $prefix
     * @param string $class
     *
     * @return string
     */
    public static function makeSelector($prefix, $class = null)
    {
        $class = $class ?: static::class;

        if (! isset(static::$selectors[$class])) {
            static::$selectors[$class] = uniqid($prefix);
        }

        return static::$selectors[$class];
    }

    /**
     * @param string|array $class
     *
     * @return $this
     */
    public function addHtmlClass($class)
    {
        $this->htmlClasses = array_merge($this->htmlClasses, (array) $class);

        return $this;
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
        return <<<HTML
<a {$this->formatHtmlAttributes()}>{$this->title()}</a>
HTML;
    }

    /**
     * @return void
     */
    protected function setupHandler()
    {
        if (
            ! $this->usingHandler
            || ! method_exists($this, 'handle')
        ) {
            return;
        }

        if ($confirm = $this->confirm()) {
            $this->setHtmlAttribute('data-confirm', $confirm);
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

        $this->setupHandler();
        $this->addScript();
        $this->setupHtmlAttributes();

        return $this->html();
    }

    /**
     * @return string
     */
    protected function formatHtmlClasses()
    {
        return implode(' ', array_unique($this->htmlClasses));
    }

    /**
     * @return void
     */
    protected function setupHtmlAttributes()
    {
        $this->addHtmlClass($this->elementClass());

        $attributes = [
            'class' => $this->formatHtmlClasses(),
        ];

        if (method_exists($this, 'href') && ($href = $this->href())) {
            $this->usingHandler = false;

            $attributes['href'] = $href;
        }

        $this->defaultHtmlAttribute('style', 'cursor: pointer');
        $this->setHtmlAttribute($attributes);
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
