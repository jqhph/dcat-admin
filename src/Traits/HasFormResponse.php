<?php

namespace Dcat\Admin\Traits;

use Dcat\Admin\Admin;
use Dcat\Admin\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

trait HasFormResponse
{
    protected $currentUrl;

    /**
     * @return JsonResponse
     */
    public function response()
    {
        return Admin::json();
    }

    /**
     * 返回字段验证错误信息.
     *
     * @param  array|MessageBag|\Illuminate\Validation\Validator  $validationMessages
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function validationErrorsResponse($validationMessages)
    {
        return $this
            ->response()
            ->withValidation($validationMessages)
            ->send();
    }

    /**
     * 设置当前URL.
     *
     * @param  string  $url
     * @return $this
     */
    public function setCurrentUrl($url)
    {
        $this->currentUrl = admin_url($url);

        return $this;
    }

    /**
     * 获取当前URL.
     *
     * @param  string|null  $default
     * @param  Request|null  $request
     * @return string
     */
    protected function getCurrentUrl($default = null, Request $request = null)
    {
        if ($this->currentUrl) {
            return admin_url($this->currentUrl);
        }

        /* @var Request $request */
        $request = $request ?: (empty($this->request) ? request() : $this->request);

        if ($current = $request->get(static::CURRENT_URL_NAME)) {
            return admin_url($current);
        }

        if ($default !== null) {
            return $default;
        }

        $query = $request->query();

        if (method_exists($this, 'sanitize')) {
            $query = $this->sanitize($query);
        }

        return url($request->path().'?'.http_build_query($query));
    }

    /**
     * 响应数据.
     *
     * @param $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResponse($response)
    {
        if ($response instanceof JsonResponse) {
            return $response->send();
        }

        return $response;
    }
}
