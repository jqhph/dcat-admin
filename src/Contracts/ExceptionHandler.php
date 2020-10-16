<?php

namespace Dcat\Admin\Contracts;

interface ExceptionHandler
{
    /**
     * 处理异常.
     *
     * @param \Throwable $e
     *
     * @return array|string|void
     */
    public function handle(\Throwable $e);

    /**
     * 显示异常信息.
     *
     * @param \Throwable $exception
     *
     * @return array|string|void
     */
    public function render(\Throwable $exception);

    /**
     * 上报异常信息.
     *
     * @param \Throwable $e
     */
    public function report(\Throwable $e);
}
