<?php

namespace Dcat\Admin\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Support\RemoteRenderable;
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
        $class = $request->get('renderable');
        $key = $request->get('key');

        $class = str_replace('_', '\\', $class);

        if (class_exists($class)) {
            return $this->render(new $class($key));
        }

        return $class;
    }

    protected function render($renderable)
    {
        return Helper::render($renderable->render())
            .Admin::asset()->scriptToHtml()
            .Admin::asset()->styleToHtml();
    }
}