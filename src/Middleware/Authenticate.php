<?php

namespace Dcat\Admin\Middleware;

use Closure;
use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Illuminate\Http\Request;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (
            ! config('admin.auth.enable', true)
            || ! Admin::guard()->guest()
            || $this->shouldPassThrough($request)
        ) {
            return $next($request);
        }

        $loginPage = admin_base_path('auth/login');

        if ($request->ajax() && ! $request->pjax()) {
            return response()->json(['message' => 'Unauthorized.', 'login' => $loginPage], 401);
        }

        if ($request->pjax()) {
            return response("<script>location.href = '$loginPage';</script>");
        }

        return redirect()->guest($loginPage);
    }

    /**
     * Determine if the request has a URI that should pass through verification.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function shouldPassThrough($request)
    {
        foreach (config('admin.auth.except', []) as $except) {
            $except = admin_base_path($except);

            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if (Helper::matchRequestPath($except)) {
                return true;
            }
        }

        return false;
    }
}
