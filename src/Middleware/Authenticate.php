<?php

namespace Dcat\Admin\Middleware;

use Closure;
use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

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
        if (Admin::guard()->guest() && !$this->shouldPassThrough($request)) {
            $loginPage = admin_base_path('auth/login');

            if ($request->ajax() && !$request->pjax()) {
                return response()->json(['message' => 'Unauthorized.', 'login' => $loginPage], 401);
            }

            $response = redirect()->guest($loginPage);

            if ($request->pjax()) {
                $response->headers->remove('Location');
                $response->setStatusCode(200);

                return $response->setContent("<script>location.href = '$loginPage';</script>");
            }

            return $response;
        }

        return $next($request);
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
