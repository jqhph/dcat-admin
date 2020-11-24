<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\InteractsWithRenderApi;
use Illuminate\Support\Str;

class Lazy extends Widget
{
    use InteractsWithRenderApi;

    protected $target = 'lazy';
    protected $load = true;

    public function __construct(LazyRenderable $renderable = null, bool $load = true)
    {
        $this->setRenderable($renderable);
        $this->load($load);

        $this->class('lazy-box');
        $this->id('lazy-'.Str::random(8));
    }

    /**
     * 设置是否立即加载.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function load(bool $value)
    {
        $this->load = $value;

        return $this;
    }

    /**
     * 获取触发异步渲染JS代码.
     *
     * @return string
     */
    public function getLoadScript()
    {
        return "$('{$this->getElementSelector()}').trigger('{$this->target}:load');";
    }

    protected function addScript()
    {
        $loader = $this->load ? "target.trigger('{$this->target}:load')" : '';

        $this->script = <<<JS
(function () {
    var target = $('{$this->getElementSelector()}'), body = target;
    {$this->getRenderableScript()}

    body.html('<div style="min-height:150px"></div>').loading();
    
    {$loader}
})();
JS;
    }

    public function html()
    {
        $this->addScript();

        return <<<HTML
<div {$this->formatHtmlAttributes()}></div>
HTML;
    }
}
