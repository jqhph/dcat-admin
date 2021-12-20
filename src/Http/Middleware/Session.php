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

        $path_prefix = '';
        $path_arr = parse_url(config('app.url'));

        if (array_key_exists('path', $path_arr) && ! empty($path_arr['path'])) {
            $path_prefix = rtrim($path_arr['path'], '/');
        }

        $path = $path_prefix.'/'.trim(config('admin.route.prefix'), '/');

        config(['session.path' => $path]);

        return $next($request);
    }
}
