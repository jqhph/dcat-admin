<?php

namespace Dcat\Admin\Middleware;

use Dcat\Admin\Admin;
use Illuminate\Http\Request;

class Bootstrap
{
    public function handle(Request $request, \Closure $next)
    {
        $this->includeBootstrapFile();
        $this->setupScript();
        $this->fireEvents();

        return $next($request);
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
        Admin::script("Dcat.token = \"$token\";console.log(56565656)");
    }

    protected function fireEvents()
    {
        Admin::callBooting();

        Admin::callBooted();
    }
}
