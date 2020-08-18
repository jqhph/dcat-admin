<?php

namespace Dcat\Admin\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Support\LazyRenderable;
use Illuminate\Http\Request;

class RenderableController
{
    /**
     * @param Request $request
     *
     * @return mixed|string
     */
    public function handle(Request $request)
    {
        $renderable = $this->newRenderable($request);

        $renderable::collectAssets();

        $this->addScript();

        $this->forgetDefaultAssets();

        return $this->render($renderable);
    }

    protected function render(LazyRenderable $renderable)
    {
        $asset = Admin::asset();

        return Helper::render($renderable->render())
            .Admin::html()
            .$asset->jsToHtml()
            .$asset->cssToHtml()
            .$asset->scriptToHtml()
            .$asset->styleToHtml();
    }

    protected function newRenderable(Request $request)
    {
        $class = $request->get('renderable');

        $class = str_replace('_', '\\', $class);

        return new $class($request->all());
    }

    protected function addScript()
    {
        Admin::script('Dcat.pjaxResponded()', true);
    }

    protected function forgetDefaultAssets()
    {
        Admin::baseJs([]);
        Admin::baseCss([]);
        Admin::fonts([]);
    }
}
