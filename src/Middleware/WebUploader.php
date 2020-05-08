<?php

namespace Dcat\Admin\Middleware;

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
            if (! $file = $webUploader->getCompleteUploadedFile()) {
                // 分块未上传完毕，返回已合并成功信息
                return response()->json(['merge' => 1]);
            }

            $response = $next($request);

            // 移除临时文件
            $webUploader->deleteTempFile();

            return $response;
        } catch (\Throwable $e) {
            // 移除临时文件
            $webUploader->deleteTempFile();

            throw $e;
        }
    }
}
