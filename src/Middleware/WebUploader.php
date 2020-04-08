<?php

namespace Dcat\Admin\Middleware;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\WebUploader as Uploader;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * 文件分块上传合并处理中间件.
 *
 * Class WebUploader
 * @package Dcat\Admin\Middleware
 */
class WebUploader
{
    public function handle(Request $request, \Closure $next)
    {
        Admin::context()->webUploader = $webUploader = new Uploader();

        if (! $webUploader->isUploading()) {
            return $next($request);
        }

        try {
            if (! $file = $webUploader->getCompleteUploadedFile()) {
                // 分块未上传完毕，返回已合并成功信息
                return $webUploader->responseMerged();
            }
        } catch (FileException $e) {
            $webUploader->deleteTempFile();

            throw $e;
        }

        return $next($request);
    }
}
