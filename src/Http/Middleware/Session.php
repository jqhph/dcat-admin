<?php

namespace Dcat\Admin\Http\Middleware;

use Illuminate\Http\Request;

class Session
{
    public function handle(Request $request, \Closure $next)
    {
        if (! config('admin.route.enable_session_middleware') && ! config('admin.multi_app')) {
            return $next($request);
        }

        $path = '/'.trim(config('admin.route.prefix'), '/');

        config(['session.path' => $path]);

        if ($domain = config('admin.route.domain')) {
            config(['session.domain' => $domain]);
        }

        return $next($request);
    }
}
