<?php

namespace Dcat\Admin\Traits;

use Dcat\Admin\Contracts\LazyRenderable;

/**
 * @property string $target
 */
trait InteractsWithRenderApi
{
    /**
     * @var LazyRenderable
     */
    protected $renderable;

    /**
     * @var string
     */
    protected $loadScript;

    /**
     * 监听异步渲染完成事件.
     *
     * @param  string  $script
     * @return $this
     */
    public function onLoad(string $script)
    {
        $this->loadScript .= ";{$script}";

        return $this;
    }

    public function getRenderable()
    {
        return $this->renderable;
    }

    public function setRenderable(?LazyRenderable $renderable)
    {
        $this->renderable = $renderable;

        return $this;
    }

    protected function getRenderableScript()
    {
        if (! $this->getRenderable()) {
            return;
        }

        $url = $this->renderable->getUrl();

        return <<<JS
target.on('{$this->target}:load', function () {
    Dcat.helpers.asyncRender('{$url}', function (html) {
        body.html(html);
        
        {$this->loadScript}
        
        target.trigger('{$this->target}:loaded');
    });
});
JS;
    }
}
