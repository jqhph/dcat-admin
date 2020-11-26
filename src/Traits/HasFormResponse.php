<?php

namespace Dcat\Admin\Traits;

use Dcat\Admin\Support\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator;

trait HasFormResponse
{
    protected $currentUrl;

    /**
     * Get ajax response.
     *
     * @param $message
     * @param null $redirect
     * @param bool $status
     * @param array $options
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function ajaxResponse(
        ?string $message,
        ?string $redirect = null,
        bool $status = true,
        array $options = []
    ) {
        $location = $options['location'] ?? false;
        $urlKey = $location ? 'location' : 'redirect';

        return response()->json([
            'status'   => $status,
            'message'  => $message,
            $urlKey    => $redirect ? admin_url($redirect) : '',
        ]);
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
    public function location($url = null, $options = [])
    {
        if (is_string($options)) {
            $options = ['message' => $options];
        }
        $options['location'] = true;

        return $this->redirect($url, $options);
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
     * 设置当前URL.
     *
     * @param string $url
     *
     * @return $this
     */
    public function setCurrentUrl($url)
    {
        $this->currentUrl = admin_url($url);

        return $this;
    }

    /**
     * @param Request|null $request
     *
     * @return string
     */
    protected function getCurrentUrl(Request $request = null)
    {
        if ($this->currentUrl) {
            return admin_url($this->currentUrl);
        }

        /* @var Request $request */
        $request = $request ?: (empty($this->request) ? request() : $this->request);

        if ($current = $request->get(static::CURRENT_URL_NAME)) {
            return admin_url($current);
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
     * @param string|array $url
     * @param array|string $options
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function redirect($url = null, $options = null)
    {
        if (is_array($url)) {
            $options = $url;
            $url = null;
        }

        if (is_string($options)) {
            $message = $options;
            $options = [];
        } else {
            $message = $options['message'] ?? null;
        }

        $status = (bool) ($options['status'] ?? true);

        if ($this->isAjaxRequest()) {
            $message = $message ?: trans('admin.save_succeeded');

            return $this->ajaxResponse($message, $url, $status, $options);
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
