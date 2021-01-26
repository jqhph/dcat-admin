<?php

namespace Dcat\Admin\Http;

use Dcat\Admin\Exception\AdminException;
use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Class JsonResponse.
 *
 * @method $this successIf($condition, ?string $message)
 * @method $this errorIf($condition, ?string $message)
 * @method $this warningIf($condition, ?string $message)
 * @method $this infoIf($condition, ?string $message)
 * @method $this detailIf($condition, ?string $message)
 * @method $this statusCodeIf($condition, int $code)
 * @method $this redirectIf($condition, ?string $url)
 * @method $this locationIf($condition, ?string $url = null)
 * @method $this refreshIf($condition)
 * @method $this downloadIf($condition, ?string $url)
 * @method $this scriptIf($condition, ?string $script)
 * @method $this alertIf($condition, bool $alert = true)
 * @method $this htmlIf($condition, $html)
 * @method $this dataIf($condition, array $data)
 * @method $this optionsIf($condition, array $data)
 * @method $this withValidationIf($condition, $errors)
 * @method $this withExceptionIf($condition, \Throwable $e)
 */
class JsonResponse implements Arrayable
{
    protected $status = true;
    protected $statusCode = 200;
    protected $exception;
    protected $data = [];
    protected $html;
    protected $options = [];

    public function __construct(array $data = [])
    {
        $this->data($data);
    }

    /**
     * 设置请求结果是否成功.
     *
     * @param bool $status
     *
     * @return $this
     */
    public function status(bool $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * 设置 HTTP 状态码.
     *
     * @param int $statusCode
     *
     * @return $this
     */
    public function statusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * 设置提示信息.
     *
     * @param string $message
     *
     * @return $this
     */
    public function message(?string $message)
    {
        $this->data['message'] = $message;

        return $this;
    }

    /**
     * 显示 成功 提示弹窗.
     *
     * @param string $message
     *
     * @return $this
     */
    public function success(?string $message)
    {
        $this->status(true);

        return $this->show('success', $message);
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function info(?string $message)
    {
        return $this->show('info', $message);
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function warning(?string $message)
    {
        return $this->show('warning', $message);
    }

    /**
     * 显示 错误 信息弹窗.
     *
     * @param string $message
     * @param bool   $alert
     *
     * @return $this
     */
    public function error(?string $message)
    {
        $this->status(false);

        return $this->show('error', $message);
    }

    /**
     * 设置 toastr 显示时长.
     *
     * @param $seconds
     *
     * @return $this
     */
    public function timeout($seconds)
    {
        return $this->data(['timeout' => $seconds]);
    }

    /**
     * 显示确认弹窗.
     *
     * @param bool $alert
     *
     * @return $this
     */
    public function alert(bool $alert = true)
    {
        return $this->data(['alert' => $alert]);
    }

    /**
     * 显示弹窗描述信息.
     *
     * @param string $detail
     *
     * @return $this
     */
    public function detail(?string $detail)
    {
        return $this->data(['detail' => $detail]);
    }

    /**
     * 显示弹窗信息.
     *
     * @param string $type
     * @param string $message
     *
     * @return $this
     */
    protected function show(?string $type, ?string $message = null)
    {
        if ($message) {
            $this->message($message);
        }

        return $this->data(['type' => $type]);
    }

    /**
     * 跳转.
     *
     * @param string $url
     *
     * @return $this
     */
    public function redirect(?string $url)
    {
        return $this->then(['action' => 'redirect', 'value' => admin_url($url)]);
    }

    /**
     * @param string|null $url
     *
     * @return $this
     */
    public function redirectToIntended(?string $url)
    {
        $path = session()->pull('url.intended');

        return $this->redirect($path ?: $url);
    }

    /**
     * Location 跳转.
     *
     * @param string $location 不传则刷新当前页面
     *
     * @return $this
     */
    public function location(?string $location = null)
    {
        return $this->then(['action' => 'location', 'value' => $location ? admin_url($location) : null]);
    }

    /**
     * @param string|null $url
     *
     * @return $this
     */
    public function locationToIntended(?string $url)
    {
        $path = session()->pull('url.intended');

        return $this->location($path ?: $url);
    }

    /**
     * 下载.
     *
     * @param string $url
     *
     * @return $this
     */
    public function download($url)
    {
        return $this->then(['action' => 'download', 'value' => admin_url($url)]);
    }

    /**
     * 刷新页面.
     *
     * @return $this
     */
    public function refresh()
    {
        return $this->then(['action' => 'refresh', 'value' => true]);
    }

    /**
     * 执行JS代码.
     *
     * @param string $script
     *
     * @return $this
     */
    public function script($script)
    {
        return $this->then(['action' => 'script', 'value' => $script]);
    }

    /**
     * @param array $value
     *
     * @return $this
     */
    protected function then(array $value)
    {
        $this->data['then'] = $value;

        return $this;
    }

    /**
     * 设置返回数据.
     *
     * @param array $value
     *
     * @return $this
     */
    public function data(array $value)
    {
        $this->data = array_merge($this->data, $value);

        return $this;
    }

    /**
     * 返回 HTML.
     *
     * @param string $html
     *
     * @return $this
     */
    public function html($html)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * 设置其他字段.
     *
     * @param array $options
     *
     * @return $this
     */
    public function options(array $options)
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * 设置字段验证错误信息.
     *
     * @param $errors
     *
     * @return $this
     */
    public function withValidation($errors)
    {
        if ($errors instanceof Validator) {
            $errors = $errors->errors();
        }

        if ($errors instanceof MessageBag) {
            $errors = $errors->getMessages();
        }

        return $this
            ->status(false)
            ->statusCode(422)
            ->options(['errors' => $errors]);
    }

    /**
     * 响应异常.
     *
     * @param \Throwable $exception
     *
     * @return $this
     */
    public function withException(\Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return $this->withValidation($exception->errors());
        }

        return $this
            ->status(false)
            ->error(
                sprintf('[%s] %s', get_class($exception), $exception->getMessage())
            );
    }

    /**
     * Flash a piece of data to the session.
     *
     * @param  string|array  $key
     * @param  mixed  $value
     * @return $this
     */
    public function with($key, $value = null)
    {
        $key = is_array($key) ? $key : [$key => $value];

        foreach ($key as $k => $v) {
            session()->flash($k, $v);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = ['status' => $this->status, 'data' => $this->data];

        if ($this->html) {
            $data['html'] = Helper::render($this->html);
        }

        return $data + $this->options;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function send()
    {
        return response()->json($this->toArray(), $this->statusCode);
    }

    public function __call($method, $arguments)
    {
        if (Str::endsWith($method, 'If')) {
            if ($arguments) {
                $method = Str::replaceLast('If', '', $method);

                $condition = value(array_shift($arguments));

                return $condition ? $this->$method(...$arguments) : $this;
            }
        }

        throw new AdminException(sprintf('Call to undefined method "%s"', $method));
    }

    /**
     * @param mixed ...$params
     *
     * @return $this
     */
    public static function make(...$params)
    {
        return new static(...$params);
    }
}
