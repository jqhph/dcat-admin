<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Grid\LazyRenderable as LazyGrid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\HasHtmlAttributes;
use Dcat\Admin\Traits\HasVariables;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;

/**
 * @method $this class(array|string $class, bool $append = false)
 * @method $this style(string $style, bool $append = true)
 * @method $this id(string $id = null)
 */
abstract class Widget implements Renderable
{
    use HasHtmlAttributes;
    use HasVariables;

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
    protected $options = [];

    /**
     * @var string
     */
    protected $elementClass;

    /**
     * @var bool
     */
    protected $runScript = true;

    /**
     * @param  mixed  ...$params
     * @return static
     */
    public static function make(...$params)
    {
        return new static(...$params);
    }

    /**
     * 符合条件则执行.
     *
     * @param  mixed  $value
     * @param  callable  $callback
     * @return $this|mixed
     */
    public function when($value, $callback)
    {
        if ($value) {
            return $callback($this, $value) ?: $this;
        }

        return $this;
    }

    /**
     * 批量设置选项.
     *
     * @param  array  $options
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

    /**
     * 设置或获取配置选项.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function option($key, $value = null)
    {
        if ($value === null) {
            return Arr::get($this->options, $key);
        }

        Arr::set($this->options, $key, $value);

        return $this;
    }

    /**
     * 获取所有选项.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * 获取视图变量.
     *
     * @return array
     */
    public function defaultVariables()
    {
        return [
            'attributes' => $this->formatHtmlAttributes(),
            'options'    => $this->options,
            'class'      => $this->getElementClass(),
            'selector'   => $this->getElementSelector(),
        ];
    }

    /**
     * 收集静态资源.
     */
    public static function requireAssets()
    {
        static::$js && Admin::js(static::$js);
        static::$css && Admin::css(static::$css);
    }

    /**
     * 运行JS.
     */
    protected function withScript()
    {
        if ($this->runScript && $this->script) {
            Admin::script($this->script);
        }
    }

    /**
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
        static::requireAssets();

        $this->class($this->getElementClass(), true);

        $html = $this->html();

        $this->withScript();

        return $html;
    }

    /**
     * 获取元素选择器.
     *
     * @return string
     */
    public function getElementSelector()
    {
        return '.'.$this->getElementClass();
    }

    /**
     * @param  string  $elementClass
     * @return $this
     */
    public function setElementClass(string $elementClass)
    {
        $this->elementClass = $elementClass;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getElementClass()
    {
        return $this->elementClass ?: str_replace('\\', '_', static::class);
    }

    /**
     * 渲染HTML.
     *
     * @return string
     */
    public function html()
    {
        if (! $this->view) {
            return;
        }

        $result = Admin::resolveHtml(view($this->view, $this->variables()), ['runScript' => $this->runScript]);

        $this->script .= $result['script'];

        return $result['html'];
    }

    /**
     * 自动调用render方法.
     *
     * @return void
     */
    protected function autoRender()
    {
        Content::composed(function () {
            if ($results = Helper::render($this->render())) {
                Admin::html($results);
            }
        });
    }

    /**
     * 设置模板.
     *
     * @param  string  $view
     */
    public function view($view)
    {
        $this->view = $view;
    }

    /**
     * 设置是否执行JS代码.
     *
     * @param  bool  $run
     * @return $this
     */
    public function runScript(bool $run = true)
    {
        $this->runScript = $run;

        return $this;
    }

    /**
     * @return string
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * @param  mixed  $content
     * @return Lazy|LazyTable|mixed
     */
    protected function formatRenderable($content)
    {
        if ($content instanceof LazyGrid) {
            return LazyTable::make($content);
        }

        if ($content instanceof LazyRenderable) {
            return Lazy::make($content);
        }

        return $content;
    }

    /**
     * @param $method
     * @param $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        if ($method === 'style' || $method === 'class') {
            $value = $parameters[0] ?? null;
            $append = $parameters[1] ?? ($method === 'class' ? false : true);

            if (is_array($value)) {
                $value = implode(' ', $value);
            }

            if ($append) {
                $original = $this->htmlAttributes[$method] ?? '';

                $de = $method === 'style' ? ';' : ' ';

                $value = $original.$de.$value;
            }

            return $this->setHtmlAttribute($method, $value);
        }

        // 获取属性
        if (count($parameters) === 0 || $parameters[0] === null) {
            return $this->getHtmlAttribute($method);
        }

        // 设置属性
        $this->setHtmlAttribute($method, $parameters[0]);

        return $this;
    }

    /**
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->htmlAttributes[$key] ?? null;
    }

    /**
     * @param  string  $key
     * @param  mixed  $value
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
