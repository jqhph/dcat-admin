<?php

namespace Dcat\Admin\Http\Middleware;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\DarkModeSwitcher;
use Illuminate\Http\Request;

class Bootstrap
{
    public function handle(Request $request, \Closure $next)
    {
        $this->includeBootstrapFile();
        $this->addScript();
        $this->fireEvents();
        $this->setUpDarkMode();

        $response = $next($request);

        $this->storeCurrentUrl($request);

        return $response;
    }

    protected function setUpDarkMode()
    {
        if (
            config('admin.layout.dark_mode_switch')
            && ! Helper::isAjaxRequest()
            && ! request()->routeIs(admin_api_route_name('*'))
        ) {
            Admin::navbar()->right((new DarkModeSwitcher())->render());
        }
    }

    protected function includeBootstrapFile()
    {
        if (is_file($bootstrap = admin_path('bootstrap.php'))) {
            require $bootstrap;
        }
    }

    protected function addScript()
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
     * @return void
     */
    protected function storeCurrentUrl(Request $request)
    {
        if (
            $request->method() === 'GET'
            && $request->route()
            && ! Helper::isAjaxRequest()
            && ! $this->prefetch($request)
        ) {
            Admin::addIgnoreQueryName(['_token', '_pjax']);

            Helper::setPreviousUrl(
                Helper::fullUrlWithoutQuery(Admin::getIgnoreQueryNames())
            );
        }
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function prefetch($request)
    {
        if (method_exists($request, 'prefetch')) {
            return $request->prefetch();
        }

        return strcasecmp($request->server->get('HTTP_X_MOZ'), 'prefetch') === 0 ||
            strcasecmp($request->headers->get('Purpose'), 'prefetch') === 0;
    }
}
