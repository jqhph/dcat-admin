<?php

namespace Dcat\Admin\Support;

use Illuminate\Http\Request;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * WebUploader文件上传处理.
 *
 * @property string $_id
 * @property int $chunk
 * @property int $chunks
 * @property string $upload_column
 * @property UploadedFile $file
 */
class WebUploader
{
    const FILE_NAME = '_file_';

    public $temporaryDirectory = 'tmp';

    protected $temporaryFilePath;

    protected $completeFile;

    public function __construct(Request $request = null)
    {
        $request = $this->prepareRequest($request ?: request());

        $this->_id = $request->get('_id');
        $this->chunk = $request->get('chunk');
        $this->chunks = $request->get('chunks');
        $this->upload_column = $request->get('upload_column');
        $this->file = $request->file(static::FILE_NAME);
    }

    protected function prepareRequest($request)
    {
        $relation = $request->get('_relation');
        if (! $relation || ! is_string($relation)) {
            return $request;
        }

        return $request->merge([
            '_relation' => trim($relation, ','),
        ]);
    }

    /**
     * 判断是否是分块上传.
     *
     * @return bool
     */
    public function hasChunkFile()
    {
        return $this->chunks > 1;
    }

    /**
     * 判断是否是文件上传请求.
     *
     * @return bool
     */
    public function isUploading()
    {
        $file = $this->file;

        if (
            ! $file
            || ! $this->upload_column
            || ! $file instanceof UploadedFile
        ) {
            return false;
        }

        return true;
    }

    /**
     * 获取完整的上传文件.
     *
     * @return UploadedFile|void
     */
    public function getUploadedFile()
    {
        $file = $this->file;

        if (! $file || ! $file instanceof UploadedFile) {
            return;
        }

        if (! $this->hasChunkFile()) {
            return $file;
        }

        if ($this->completeFile !== null) {
            return $this->completeFile;
        }

        return $this->completeFile = $this->mergeChunks($file);
    }

    /**
     * 移除临时文件以及文件夹.
     */
    public function deleteTemporaryFile()
    {
        if (! $this->temporaryFilePath) {
            return;
        }
        @unlink($this->temporaryFilePath);

        if (
            ! Finder::create()
                ->in($dir = dirname($this->temporaryFilePath))
                ->files()
                ->count()
        ) {
            @rmdir($dir);
        }
    }

    /**
     * 合并分块文件.
     *
     * @param  UploadedFile  $file
     * @return UploadedFile|false
     */
    protected function mergeChunks(UploadedFile $file)
    {
        $tmpDir = $this->getTemporaryPath($this->_id);
        $newFilename = $this->generateChunkFileName($file);

        // 移动当前分块到临时目录.
        $this->moveChunk($file, $tmpDir, $newFilename);

        // 判断所有分块是否上传完毕.
        if (! $this->isComplete($tmpDir, $newFilename)) {
            return false;
        }

        $this->temporaryFilePath = $tmpDir.'/'.$newFilename.'.tmp';

        $this->putTempFileContent($this->temporaryFilePath, $tmpDir, $newFilename);

        return new UploadedFile(
            $this->temporaryFilePath,
            $file->getClientOriginalName(),
            null,
            null,
            true
        );
    }

    /**
     * 判断所有分块是否上传完毕.
     *
     * @param  string  $tmpDir
     * @param  string  $newFilename
     * @return bool
     */
    protected function isComplete($tmpDir, $newFilename)
    {
        for ($index = 0; $index < $this->chunks; $index++) {
            if (! is_file("{$tmpDir}/{$newFilename}.{$index}.part")) {
                return false;
            }
        }

        return true;
    }

    /**
     * 移动分块文件到临时目录.
     *
     * @param  UploadedFile  $file
     * @param  string  $tmpDir
     * @param  string  $newFilename
     */
    protected function moveChunk(UploadedFile $file, $tmpDir, $newFilename)
    {
        $file->move($tmpDir, "{$newFilename}.{$this->chunk}.part");
    }

    /**
     * @param  string  $path
     * @param  string  $tmpDir
     * @param  string  $newFilename
     */
    protected function putTempFileContent($path, $tmpDir, $newFileame)
    {
        $out = fopen($path, 'wb');

        if (flock($out, LOCK_EX)) {
            for ($index = 0; $index < $this->chunks; $index++) {
                $partPath = "{$tmpDir}/{$newFileame}.{$index}.part";
                if (! $in = @fopen($partPath, 'rb')) {
                    break;
                }

                while ($buff = fread($in, 4096)) {
                    fwrite($out, $buff);
                }

                @fclose($in);
                @unlink($partPath);
            }

            flock($out, LOCK_UN);
        }

        fclose($out);
    }

    /**
     * 生成分块文件名称.
     *
     * @param  UploadedFile  $file
     * @return string
     */
    protected function generateChunkFileName(UploadedFile $file)
    {
        return md5($file->getClientOriginalName());
    }

    /**
     * 获取临时文件路径.
     *
     * @param  mixed  $path
     * @return string
     */
    public function getTemporaryPath($path)
    {
        return $this->getTemporaryDirectory().'/'.$path;
    }

    /**
     * 获取临时文件目录.
     *
     * @return string
     */
    public function getTemporaryDirectory()
    {
        $dir = storage_path($this->temporaryDirectory);

        if (! is_dir($dir)) {
            app('files')->makeDirectory($dir, 0755, true);
        }

        return rtrim($dir, '/');
    }
}
