<?php

namespace Dcat\Admin\Http\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Support\Helper;
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

    protected function newRenderable(Request $request): LazyRenderable
    {
        $class = $request->get('renderable');

        $class = str_replace('_', '\\', $class);

        $renderable = new $class();

        $renderable->payload($request->all());

        if (method_exists($renderable, 'collectAssets')) {
            $renderable->collectAssets();
        }

        return $renderable;
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
