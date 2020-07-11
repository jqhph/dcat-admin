<?php

namespace Dcat\Admin\Traits;

use Dcat\Admin\Support\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator;

trait HasFormResponse
{
    /**
     * Get ajax response.
     *
     * @param $message
     * @param null $redirect
     * @param bool $status
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function ajaxResponse(?string $message, ?string $redirect = null, bool $status = true)
    {
        if ($this->isAjaxRequest()) {
            return response()->json([
                'status'   => $status,
                'message'  => $message,
                'redirect' => $redirect ? admin_url($redirect) : '',
            ]);
        }

        return false;
    }

    /**
     * Send a location redirect response.
     *
     * @param string|null $message
     * @param string|null $url
     * @param bool        $status
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function location(?string $message, ?string $url = null, bool $status = true)
    {
        if ($this->isAjaxRequest()) {
            return response()->json([
                'status'   => $status,
                'message'  => $message,
                'location' => $url ? admin_url($url) : false,
            ]);
        }

        if ($message) {
            admin_toastr($message, $status ? 'success' : 'error');
        }

        return $url ? redirect(admin_url($url)) : redirect()->refresh();
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    public function isAjaxRequest(Request $request = null)
    {
        return Helper::isAjaxRequest($request);
    }

    /**
     * @param string $message
     * @param string $redirectTo
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function success($message = null, $redirectTo = null)
    {
        $redirectTo = $redirectTo ?: $this->getCurrentUrl();

        return $this->redirect($redirectTo, [
            'message'     => $message,
            'status'      => true,
            'status_code' => 200,
        ]);
    }

    /**
     * @param Request|null $request
     *
     * @return string
     */
    protected function getCurrentUrl(Request $request = null)
    {
        /* @var Request $request */
        $request = $request ?: (empty($this->request) ? request() : $this->request);

        if ($current = $request->get('_current_')) {
            return url($current);
        }

        $query = $request->query();

        if (method_exists($this, 'sanitize')) {
            $query = $this->sanitize($query);
        }

        return url($request->path().'?'.http_build_query($query));
    }

    /**
     * @param string $message
     * @param string $redirectTo
     * @param int    $statusCode
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function error($message = null, $redirectTo = null, int $statusCode = 200)
    {
        if (! $redirectTo) {
            if (! $this->isAjaxRequest()) {
                admin_toastr($message, 'error');

                return back()->withInput();
            }

            return $this->ajaxResponse($message, null, false);
        }

        return $this->redirect($redirectTo, [
            'message'     => $message,
            'status'      => false,
            'status_code' => $statusCode,
        ]);
    }

    /**
     * Get redirect response.
     *
     * @param string       $url
     * @param array|string $options
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function redirect(?string $url, $options = null)
    {
        if (is_string($options)) {
            $message = $options;
            $options = [];
        } else {
            $message = $options['message'] ?? null;
        }

        $status = (bool) ($options['status'] ?? true);

        if ($this->isAjaxRequest()) {
            $message = $message ?: trans('admin.save_succeeded');

            return $this->ajaxResponse($message, $url, $status);
        }

        $statusCode = (int) ($options['status_code'] ?? 302);

        if ($message) {
            admin_toastr($message, $status ? 'success' : 'error');
        }

        return $url ? redirect(admin_url($url), $statusCode) : redirect()->back($statusCode);
    }

    /**
     * @param string|null  $url
     * @param array|string $options
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function redirectToIntended(?string $url, $options = null)
    {
        $path = session()->pull('url.intended');

        return $this->redirect($path ?: $url, $options);
    }

    /**
     * @param array|MessageBag|\Illuminate\Validation\Validator $validationMessages
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function validationErrorsResponse($validationMessages)
    {
        if ($validationMessages instanceof Validator) {
            $validationMessages = $validationMessages->getMessageBag();
        }

        if (! static::isAjaxRequest()) {
            return back()->withInput()->withErrors($validationMessages);
        }

        return response()->json([
            'errors' => is_array($validationMessages) ? $validationMessages : $validationMessages->getMessages(),
        ], 422);
    }
}
