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
     * @return string|null
     */
    public function getRequestUrl()
    {
        return optional($this->getRenderable())->getUrl();
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
}
