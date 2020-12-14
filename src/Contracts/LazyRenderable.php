<?php

namespace Dcat\Admin\Contracts;

interface LazyRenderable
{
    /**
     * 获取请求地址
     *
     * @return string
     */
    public function getUrl();

    /**
     * 渲染组件.
     *
     * @return mixed
     */
    public function render();
}
