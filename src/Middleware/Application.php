<?php

namespace Dcat\Admin\Middleware;

use Dcat\Admin\Admin;

class Application
{
    public function handle($request, \Closure $next, $app = null)
    {
        if ($app) {
            Admin::app()->current($app);
        }

        return $next($request);
    }
}
