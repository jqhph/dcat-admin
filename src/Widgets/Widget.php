<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\HasHtmlAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;

/**
 * @method $this class(string $class, bool $append = false)
 * @method $this style(string $style, bool $append = true)
 * @method $this id(string $id)
 */
abstract class Widget implements Renderable
{
    use HasHtmlAttributes;

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
    protected $variables = [];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * Create a widget instance.
     *
     * @param mixed ...$params
     *
     * @return static
     */
    public static function make(...$params)
    {
        return new static(...$params);
    }

    /**
     * @param array $options
     *
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
     * Variables in view.
     *
     * @return array
     */
    public function variables()
    {
        return array_merge($this->variables, [
            'attributes' => $this->formatHtmlAttributes(),
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
     *
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
     * @param $method
     * @param $parameters
     *
     * @return $this
     */
    public function __call($method, $parameters)
    {
        if ($method === 'style' || $method === 'class') {
            $value = $parameters[0] ?? null;
            $append = $parameters[1] ?? ($method === 'class' ? false : true);

            if ($append) {
                $original = $this->htmlAttributes[$method] ?? '';

                $de = $method === 'style' ? ';' : ' ';

                $value = $original.$de.$value;
            }

            return $this->setHtmlAttribute($method, $value);
        }

        $this->htmlAttributes[$method] = count($parameters) > 0 ? $parameters[0] : true;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->htmlAttributes[$key] ?? null;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->htmlAttributes[$key] = $value;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->render();
    }
}
