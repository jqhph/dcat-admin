<?php

namespace Dcat\Admin\Middleware;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Illuminate\Http\Request;
use Illuminate\Session\Store;

class Bootstrap
{
    public function handle(Request $request, \Closure $next)
    {
        $this->includeBootstrapFile();
        $this->setupScript();
        $this->fireEvents();

        $response = $next($request);

        $this->storeCurrentUrl($request);

        return $response;
    }

    protected function includeBootstrapFile()
    {
        if (is_file($bootstrap = admin_path('bootstrap.php'))) {
            require $bootstrap;
        }
    }

    protected function setupScript()
    {
        $token = csrf_token();
        Admin::script("Dcat.token = \"$token\";");
    }

    protected function fireEvents()
    {
        Admin::callBooting();

        Admin::callBooted();
    }

    /**
     * @param  \Illuminate\Http\Request
     *
     * @return void
     */
    protected function storeCurrentUrl(Request $request)
    {
        if (
            $request->method() === 'GET'
            && $request->route()
            && ! Helper::isAjaxRequest()
            && ! $request->prefetch()
        ) {
           Helper::setPreviousUrl($request->fullUrl());
        }
    }
}
