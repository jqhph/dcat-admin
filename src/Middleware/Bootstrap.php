<?php

namespace Dcat\Admin\Middleware;

use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Illuminate\Http\Request;

class Bootstrap
{
    public function handle(Request $request, \Closure $next)
    {
        if (is_file($bootstrap = admin_path('bootstrap.php'))) {
            require $bootstrap;
        }

        Admin::callBooting();

        Admin::callBooted();

        if (config('admin.cdn')) {
            Admin::cdn();
        }

        return $next($request);
    }

}
