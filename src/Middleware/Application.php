<?php

namespace Dcat\Admin\Middleware;

use Dcat\Admin\Admin;

class Application
{
    public function handle($request, \Closure $next, $app = null)
    {
        if ($app) {
            Admin::app()->current($app);

            $this->withSessionPath();
        }

        return $next($request);
    }

    protected function withSessionPath()
    {
        config(['session.path' => '/'.trim(config('admin.route.prefix'), '/')]);
    }
}
