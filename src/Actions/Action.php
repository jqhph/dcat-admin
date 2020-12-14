<?php

namespace Dcat\Admin\Actions;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\HasHtmlAttributes;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;

/**
 * Class Action.
 *
 * @method string href
 */
abstract class Action implements Renderable
{
    use HasHtmlAttributes;
    use HasActionHandler;

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
    protected $allowHandler = true;

    /**
     * @var array
     */
    protected $htmlClasses = [];

    /**
     * Action constructor.
     *
     * @param string $title
     */
    public function __construct($title = null)
    {
        if ($title) {
            $this->title = $title;
        }
    }

    /**
     * 是否禁用动作.
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
    public function getKey()
    {
        return $this->primaryKey;
    }

    /**
     * 设置主键.
     *
     * @param mixed $key
     *
     * @return $this
     */
    public function setKey($key)
    {
        $this->primaryKey = $key;

        return $this;
    }

    /**
     * @return string
     */
    protected function getElementClass()
    {
        return ltrim($this->selector(), '.');
    }

    /**
     * 获取动作标题.
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
        return $this->selector ?: ($this->selector = $this->makeSelector());
    }

    /**
     * 生成选择器.
     *
     * @param string $prefix
     *
     * @return string
     */
    public function makeSelector()
    {
        return '.act-'.Str::random();
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
     * 需要执行的JS代码.
     *
     * @return string|void
     */
    protected function script()
    {
    }

    /**
     * @return string
     */
    protected function html()
    {
        $this->defaultHtmlAttribute('href', 'javascript:void(0)');

        return <<<HTML
<a {$this->formatHtmlAttributes()}>{$this->title()}</a>
HTML;
    }

    /**
     * @return void
     */
    protected function prepareHandler()
    {
        if (
            ! $this->allowHandler
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

        $this->prepareHandler();

        $this->setUpHtmlAttributes();

        if ($script = $this->script()) {
            Admin::script($script);
        }

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
    protected function setUpHtmlAttributes()
    {
        $this->addHtmlClass($this->getElementClass());

        $attributes = [
            'class' => $this->formatHtmlClasses(),
        ];

        if (method_exists($this, 'href') && ($href = $this->href())) {
            $this->allowHandler = false;

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
     * @param mixed ...$params
     *
     * @return $this
     */
    public static function make(...$params)
    {
        return new static(...$params);
    }
}
