<?php

namespace Dcat\Admin\Traits;

use Dcat\Admin\Support\LazyRenderable;

trait AsyncRenderable
{
    /**
     * @var LazyRenderable
     */
    protected $renderable;

    /**
     * 获取请求地址
     *
     * @return string
     */
    public function getRequestUrl()
    {
        return $this->getRenderable()->getUrl();
    }

    /**
     * @param LazyRenderable $renderable
     *
     * @return $this
     */
    public function setRenderable(LazyRenderable $renderable)
    {
        $this->renderable = $renderable;

        return $this;
    }

    /**
     * @return LazyRenderable
     */
    public function getRenderable()
    {
        return $this->renderable;
    }

    /**
     * @return string
     */
    protected function getRenderableScript()
    {
        if (! $this->getRenderable()) {
            return;
        }

        return <<<JS
function render(callback) {
    $.ajax('{$this->getRequestUrl()}').then(function (data) {
        _loading = 0;
        
        callback(
            Dcat.assets.executeScripts(data, function () {
                Dcat.triggerReady();
            }).render()
        );
    })
}
JS;
    }
}
