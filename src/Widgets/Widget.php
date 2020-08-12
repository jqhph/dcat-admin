<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Admin;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\HasHtmlAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;

/**
 * @method $this class(string $class, bool $append = false)
 * @method $this style(string $style, bool $append = true)
 * @method $this id(string $id = null)
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
     * @param mixed ...$params
     *
     * @return static
     */
    public static function make(...$params)
    {
        return new static(...$params);
    }

    /**
     * 批量设置选项.
     *
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

    /**
     * 设置或获取配置选项.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function option($key, $value = null)
    {
        if ($value === null) {
            return Arr::get($this->options, $key);
        } else {
            Arr::set($this->options, $key, $value);
        }

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
    public function variables()
    {
        return array_merge($this->variables, [
            'attributes' => $this->formatHtmlAttributes(),
            'options'    => $this->options,
        ]);
    }

    /**
     * 设置视图变量.
     *
     * @param string|array $key
     * @param mixed        $value
     *
     * @return $this
     */
    public function with($key, $value = null)
    {
        if(is_array($key)) {
            $this->variables = array_merge($this->variables, $key);
        } else {
            $this->variables[$key] = $value;
        }

        return $this;
    }

    /**
     * 收集静态资源.
     */
    protected function collectAssets()
    {
        $this->script && Admin::script($this->script);

        static::$js && Admin::js(static::$js);
        static::$css && Admin::css(static::$css);
    }

    /**
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

        return $this->html();
    }

    /**
     * @return string
     */
    public function html()
    {
        return view($this->view, $this->variables())->render();
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

        // 获取属性
        if (count($parameters) === 0) {
            return $this->getHtmlAttribute($method);
        }

        // 设置属性
        $this->setHtmlAttribute($method, $parameters[0]);

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
