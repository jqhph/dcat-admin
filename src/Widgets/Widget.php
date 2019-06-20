<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;

/**
 * @method $this class(string $class, bool $append = false)
 * @method $this style(string $style, bool $append = true)
 * @method $this id(string $id)
 */
abstract class Widget implements Renderable
{
    /**
     * @var array
     */
    public static $css = [];

    /**
     * @var array
     */
    public static $js = [];

    /**
     * @var string
     */
    protected $view;

    /**
     * @var string
     */
    protected $script = '';

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * Create a widget instance.
     *
     * @param mixed ...$params
     * @return static
     */
    public static function make(...$params)
    {
        return new static(...$params);
    }

    /**
     *
     * @param array $options
     * @return $this
     */
    public function options($options = [])
    {
        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        $this->options = array_merge($this->options, $options);

        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get the attributes from the fluent instance.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Variables in view.
     *
     * @return array
     */
    public function variables()
    {
        return array_merge($this->variables, [
            'attributes' => $this->formatAttributes(),
            'options'    => $this->options,
        ]);
    }

    /**
     * Collect assets.
     */
    protected function collectAssets()
    {
        $this->script && Admin::script($this->script);
        static::$js && Admin::js(static::$js);
        static::$css && Admin::css(static::$css);
    }

    /**
     * To string.
     *
     * @param $value
     * @return string
     */
    protected function toString($value)
    {
        return Helper::render($value);
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->collectAssets();

        return view($this->view, $this->variables())->render();
    }

    /**
     * Set view of widget.
     *
     * @param string $view
     */
    public function view($view)
    {
        $this->view = $view;
    }

    /**
     * @return string
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * Set default attribute.
     *
     * @param string $attribute
     * @param mixed $value
     * @return $this
     */
    public function defaultAttribute($attribute, $value)
    {
        if (!array_key_exists($attribute, $this->attributes)) {
            $this->setAttribute($attribute, $value);
        }

        return $this;
    }

    /**
     * Set element attributes.
     *
     * @param $k
     * @param null $v
     * @return $this
     */
    public function setAttribute($k, $v = null)
    {
        if (is_array($k)) {
            $this->attributes = array_merge($this->attributes, $k);
        } else {
            $this->attributes[$k] = $v;
        }

        return $this;
    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @return string
     */
    public function formatAttributes()
    {
        return Helper::buildHtmlAttributes($this->getAttributes());
    }

    /**
     * @param $method
     * @param $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        if ($method === 'style' || $method === 'class') {
            $value  = $parameters[0] ?? null;
            $append = $parameters[1] ?? ($method === 'class' ? false : true);

            if ($append) {
                $original = $this->attributes[$method] ?? '';

                $de = $method === 'style' ? ';' : ' ';

                $value = $original . $de . $value;
            }

            return $this->setAttribute($method, $value);
        }

        $this->attributes[$method] = count($parameters) > 0 ? $parameters[0] : true;

        return $this;
    }

    /**
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->render();
    }

}
