<?php

namespace Dcat\Admin\Http\Middleware;

use Closure;
use Dcat\Admin\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Response;

class Pjax
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (! $request->pjax() || $response->isRedirection() || Admin::guard()->guest()) {
            return $response;
        }

        if (! $response->isSuccessful()) {
            return $this->handleErrorResponse($response);
        }

        try {
            $this->setUriHeader($response, $request);
        } catch (\Exception $exception) {
        }

        return $response;
    }

    /**
     * Handle Response with exceptions.
     *
     * @param  Response  $response
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function handleErrorResponse(Response $response)
    {
        if (config('app.debug')) {
            throw $response->exception;
        }

        $exception = $response->exception;

        $error = new MessageBag([
            'type'    => get_class($exception),
            'message' => $exception->getMessage(),
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine(),
        ]);

        return back()->withInput()->withErrors($error, 'exception');
    }

    /**
     * Set the PJAX-URL header to the current uri.
     *
     * @param  Response  $response
     * @param  Request  $request
     */
    protected function setUriHeader(Response $response, Request $request)
    {
        $response->header(
            'X-PJAX-URL',
            $request->getRequestUri()
        );
    }
}
