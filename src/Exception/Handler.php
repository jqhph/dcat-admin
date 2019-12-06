<?php

namespace Dcat\Admin\Exception;

use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;

class Handler
{
    /**
     * Render exception.
     *
     * @param \Exception $exception
     *
     * @return string
     */
    public function renderException(\Throwable $exception)
    {
        if (config('app.debug')) {
            throw $exception;
        }

        $error = new MessageBag([
            'type'    => get_class($exception),
            'message' => $exception->getMessage(),
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine(),
        ]);

        $errors = new ViewErrorBag();
        $errors->put('exception', $error);

        return view('admin::partials.exception', compact('errors'))->render();
    }

    /**
     * @param \Throwable $e
     *
     * @return mixed
     */
    public function handleDestroyException(\Throwable $e)
    {
        $root = dirname(app_path());

        $context = [
            'trace' => str_replace($root, '', $e->getTraceAsString()),
        ];

        $message = sprintf(
            '[%s] %s in %s(%s)',
            get_class($e),
            $e->getMessage(),
            str_replace($root, '', $e->getFile()),
            $e->getLine()
        );

        logger()->error($message, $context);
    }
}
