<?php

namespace Dcat\Admin\Http\Middleware;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\WebUploader as Uploader;
use Illuminate\Http\Request;

/**
 * 文件分块上传合并处理中间件.
 *
 * Class WebUploader
 */
class WebUploader
{
    public function handle(Request $request, \Closure $next)
    {
        /* @var Uploader $webUploader */
        $webUploader = app('admin.web-uploader');

        if (! $webUploader->isUploading()) {
            return $next($request);
        }

        try {
            if (! $file = $webUploader->getUploadedFile()) {
                // 分块未上传完毕，返回已合并成功信息
                return Admin::json(['merge' => 1])->send();
            }

            return $next($request);
        } finally {
            // 移除临时文件
            $webUploader->deleteTemporaryFile();
        }
    }
}
