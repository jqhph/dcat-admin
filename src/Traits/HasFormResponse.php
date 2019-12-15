<?php

namespace Dcat\Admin\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

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
    public function ajaxResponse($message, $redirect = null, bool $status = true)
    {
        if ($this->isAjaxRequest()) {
            return response()->json([
                'status'   => $status,
                'message'  => $message,
                'redirect' => admin_url($redirect),
            ]);
        }

        return false;
    }

    /**
     * Ajax but not pjax.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function isAjaxRequest(Request $request = null)
    {
        /* @var Request $request */
        $request = $request ?: (empty($this->request) ? request() : $this->request);

        return $request->ajax() && ! $request->pjax();
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
        $redirectTo = $redirectTo ?: $this->getCurrentUrl();

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

        $status = (int) ($options['status_code'] ?? 302);

        if ($message) {
            admin_alert($message);
        }

        return redirect(admin_url($url), $status);
    }

    /**
     * @param array|MessageBag $validationMessages
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function validationErrorsResponse($validationMessages)
    {
        if (! $this->isAjaxRequest()) {
            return back()->withInput()->withErrors($validationMessages);
        }

        return response()->json([
            'errors' => is_array($validationMessages) ? $validationMessages : $validationMessages->getMessages(),
        ], 422);
    }
}
