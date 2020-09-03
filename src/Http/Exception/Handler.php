<?php

namespace Dcat\Admin\Http\Exception;

use Dcat\Admin\Support\Helper;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

class Handler
{
    public function handle(\Throwable $e)
    {
        $this->report($e);

        return $this->render($e);
    }

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

    public function report(\Throwable $e)
    {
        $this->logger()->error($this->convertExceptionToString($e));
    }

    protected function convertExceptionToString(\Throwable $e)
    {
        return sprintf(
            "[%s] %s, called in %s(%s)\n%s",
            get_class($e),
            $e->getMessage(),
            $this->replaceBasePath($e->getFile()),
            $e->getLine(),
            $this->replaceBasePath($e->getTraceAsString())
        );
    }

    protected function replaceBasePath(string $path)
    {
        return str_replace(
            str_replace('\\', '/', base_path().'/'),
            '',
            str_replace('\\', '/', $path)
        );
    }

    protected function logger()
    {
        return logger();
    }
}
