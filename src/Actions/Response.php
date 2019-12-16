<?php

namespace Dcat\Admin\Actions;

use Illuminate\Validation\ValidationException;

class Response
{
    /**
     * @var bool
     */
    public $status = true;

    /**
     * @var \Exception
     */
    public $exception;

    /**
     * @var
     */
    protected $plugin;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var string
     */
    protected $html = '';

    /**
     * @param string $message
     *
     * @return $this
     */
    public function success(string $message = '')
    {
        return $this->show('success', $message);
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function info(string $message = '')
    {
        return $this->show('info', $message);
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function warning(string $message = '')
    {
        return $this->show('warning', $message);
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function error(string $message = '')
    {
        return $this->show('error', $message);
    }

    /**
     * @param string $type
     * @param string $title
     *
     * @return $this
     */
    protected function show($type, $title = '')
    {
        return $this->data(['message' => $title, 'type' => $type]);
    }

    /**
     * Send a redirect response.
     *
     * @param string $url
     *
     * @return $this
     */
    public function redirect(string $url)
    {
        return $this->then(['action' => 'redirect', 'value' => admin_url($url)]);
    }

    /**
     * Send a location redirect response.
     *
     * @param string $location
     *
     * @return $this
     */
    public function location(string $location)
    {
        return $this->then(['action' => 'location', 'value' => admin_url($location)]);
    }

    /**
     * Send a download response.
     *
     * @param string $url
     *
     * @return $this
     */
    public function download($url)
    {
        return $this->then(['action' => 'download', 'value' => $url]);
    }

    /**
     * Send a refresh response.
     *
     * @return $this
     */
    public function refresh()
    {
        return $this->then(['action' => 'refresh', 'value' => true]);
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
     * Send a html response.
     *
     * @param string $html
     *
     * @return $this
     */
    public function html($html = '')
    {
        $this->html = $html;

        return $this;
    }

    /**
     * @param \Throwable $exception
     *
     * @return $this
     */
    public static function withException(\Throwable $exception)
    {
        $response = new static();

        $response->status = false;

        if ($exception instanceof ValidationException) {
            $message = collect($exception->errors())->flatten()->implode("\n");
        } else {
            $message = $exception->getMessage();
        }

        return $response->error($message);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function send()
    {
        $data = ['status' => $this->status, 'data' => $this->data];

        if ($this->html) {
            $data['html'] = $this->html;
        }

        return response()->json($data);
    }
}
