<?php

namespace Dcat\Admin\Exception;

use Dcat\Admin\Contracts\ExceptionHandler;
use Dcat\Admin\Support\Helper;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

class Handler implements ExceptionHandler
{
    /**
     * 处理异常.
     *
     * @param \Throwable $e
     *
     * @return array|string|void
     */
    public function handle(\Throwable $e)
    {
        $this->report($e);

        return $this->render($e);
    }

    /**
     * 显示异常信息.
     *
     * @param \Throwable $exception
     *
     * @return array|string|void
     *
     * @throws \Throwable
     */
    public function render(\Throwable $exception)
    {
        if (config('app.debug')) {
            throw $exception;
        }

        if (Helper::isAjaxRequest()) {
            return;
        }

        $error = new MessageBag([
            'type'    => get_class($exception),
            'message' => $exception->getMessage(),
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine(),
            'trace'   => $this->replaceBasePath($exception->getTraceAsString()),
        ]);

        $errors = new ViewErrorBag();
        $errors->put('exception', $error);

        return view('admin::partials.exception', compact('errors'))->render();
    }

    /**
     * 上报异常信息.
     *
     * @param \Throwable $e
     */
    public function report(\Throwable $e)
    {
        report($e);
    }

    /**
     * @param string $path
     *
     * @return mixed
     */
    protected function replaceBasePath(string $path)
    {
        return str_replace(
            str_replace('\\', '/', base_path().'/'),
            '',
            str_replace('\\', '/', $path)
        );
    }
}
