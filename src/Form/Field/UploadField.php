<?php

namespace Dcat\Admin\Form\Field;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

trait UploadField
{
    /**
     * Upload directory.
     *
     * @var string
     */
    protected $directory = '';

    /**
     * File name.
     *
     * @var null
     */
    protected $name = null;

    /**
     * Storage instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $storage;

    /**
     * If use unique name to store upload file.
     *
     * @var bool
     */
    protected $useUniqueName = false;

    /**
     * If use sequence name to store upload file.
     *
     * @var bool
     */
    protected $useSequenceName = false;

    /**
     * Controls the storage permission. Could be 'private' or 'public'.
     *
     * @var string
     */
    protected $storagePermission;

    /**
     * @var string
     */
    protected $tempFilePath;

    /**
     * Retain file when delete record from DB.
     *
     * @var bool
     */
    protected $retainable = false;

    /**
     * Initialize the storage instance.
     *
     * @return void.
     */
    protected function initStorage()
    {
        $this->disk(config('admin.upload.disk'));

        if (! $this->storage) {
            $this->storage = false;
        }
    }

    /**
     * If name already exists, rename it.
     *
     * @param $file
     *
     * @return void
     */
    public function renameIfExists(UploadedFile $file)
    {
        if ($this->getStorage()->exists("{$this->getDirectory()}/$this->name")) {
            $this->name = $this->generateUniqueName($file);
        }
    }

    /**
     * @return string
     */
    protected function getUploadPath()
    {
        return "{$this->getDirectory()}/$this->name";
    }

    /**
     * Get store name of upload file.
     *
     * @param UploadedFile $file
     *
     * @return string
     */
    protected function getStoreName(UploadedFile $file)
    {
        if ($this->useUniqueName) {
            return $this->generateUniqueName($file);
        }

        if ($this->useSequenceName) {
            return $this->generateSequenceName($file);
        }

        if ($this->name instanceof \Closure) {
            return $this->name->call($this, $file);
        }

        if (is_string($this->name)) {
            return $this->name;
        }

        return $file->getClientOriginalName();
    }

    /**
     * Get directory for store file.
     *
     * @return mixed|string
     */
    public function getDirectory()
    {
        if ($this->directory instanceof \Closure) {
            return call_user_func($this->directory, $this->form);
        }

        return $this->directory ?: $this->defaultDirectory();
    }

    /**
     * Indicates if the underlying field is retainable.
     *
     * @return $this
     */
    public function retainable($retainable = true)
    {
        $this->retainable = $retainable;

        return $this;
    }

    /**
     * Upload File.
     *
     * @param UploadedFile $file
     *
     * @return Response
     */
    public function upload(UploadedFile $file)
    {
        try {
            $id = request('_id');
            if (! $id) {
                return $this->responseError(403, 'Missing id');
            }

            if (! ($file = $this->mergeChunks($id, $file))) {
                return $this->response(['merge' => 1]);
            }

            if ($errors = $this->getErrorMessages($file)) {
                $this->deleteTempFile();

                return $this->responseError(101, $errors);
            }

            $this->name = $this->getStoreName($file);

            $this->renameIfExists($file);

            $this->prepareFile($file);

            if (! is_null($this->storagePermission)) {
                $result = $this->getStorage()->putFileAs($this->getDirectory(), $file, $this->name, $this->storagePermission);
            } else {
                $result = $this->getStorage()->putFileAs($this->getDirectory(), $file, $this->name);
            }

            $this->deleteTempFile();

            if ($result) {
                $path = $this->getUploadPath();

                return $this->response([
                    'status' => true,
                    'id'     => $path,
                    'name'   => $this->name,
                    'path'   => basename($path),
                    'url'    => $this->objectUrl($path),
                ]);
            }

            return $this->responseError(107, trans('admin.upload.upload_failed'));
        } catch (\Throwable $e) {
            $this->deleteTempFile();

            throw $e;
        }
    }

    /**
     * @param UploadedFile $file
     */
    protected function prepareFile(UploadedFile $file)
    {
    }

    /**
     * @param string       $id
     * @param UploadedFile $file
     *
     * @return UploadedFile|null
     */
    protected function mergeChunks($id, UploadedFile $file)
    {
        $chunk = request('chunk', 0);
        $chunks = request('chunks', 1);

        if ($chunks <= 1) {
            return $file;
        }

        $tmpDir = $this->getTempDir($id);
        $newFilename = md5($file->getClientOriginalName());

        $file->move($tmpDir, "{$newFilename}.{$chunk}.part");

        $done = true;
        for ($index = 0; $index < $chunks; $index++) {
            if (! is_file("{$tmpDir}/{$newFilename}.{$index}.part")) {
                $done = false;
                break;
            }
        }

        if (! $done) {
            return;
        }

        $this->tempFilePath = $tmpDir.'/'.$newFilename.'.tmp';
        $this->putTempFileContent($chunks, $tmpDir, $newFilename);

        return new UploadedFile(
            $this->tempFilePath,
            $file->getClientOriginalName(),
            'application/octet-stream',
            UPLOAD_ERR_OK,
            true
        );
    }

    /**
     * Deletes the temporary file.
     */
    public function deleteTempFile()
    {
        if (! $this->tempFilePath) {
            return;
        }
        @unlink($this->tempFilePath);

        if (
            ! Finder::create()
            ->in($dir = dirname($this->tempFilePath))
            ->files()
            ->count()
        ) {
            @rmdir($dir);
        }
    }

    /**
     * @param int    $chunks
     * @param string $tmpDir
     * @param string $newFilename
     */
    protected function putTempFileContent($chunks, $tmpDir, $newFileame)
    {
        $out = fopen($this->tempFilePath, 'wb');

        if (flock($out, LOCK_EX)) {
            for ($index = 0; $index < $chunks; $index++) {
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
     * @param mixed $id
     *
     * @return string
     */
    protected function getTempDir($id)
    {
        $tmpDir = storage_path('tmp/'.$id);

        if (! is_dir($tmpDir)) {
            app('files')->makeDirectory($tmpDir, 0755, true);
        }

        return $tmpDir;
    }

    /**
     * Response the error messages.
     *
     * @param $code
     * @param $error
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseError($code, $error)
    {
        return $this->response([
            'error' => ['code' => $code, 'message' => $error], 'status' => false,
        ]);
    }

    /**
     * @param array $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function response(array $message)
    {
        return response()->json($message);
    }

    /**
     * Specify the directory and name for upload file.
     *
     * @param string      $directory
     * @param null|string $name
     *
     * @return $this
     */
    public function move($directory, $name = null)
    {
        $this->dir($directory);

        $this->name($name);

        return $this;
    }

    /**
     * Specify the directory upload file.
     *
     * @param string $dir
     *
     * @return $this
     */
    public function dir($dir)
    {
        if ($dir) {
            $this->directory = $dir;
        }

        return $this;
    }

    /**
     * Set name of store name.
     *
     * @param string|callable $name
     *
     * @return $this
     */
    public function name($name)
    {
        if ($name) {
            $this->name = $name;
        }

        return $this;
    }

    /**
     * Use unique name for store upload file.
     *
     * @return $this
     */
    public function uniqueName()
    {
        $this->useUniqueName = true;

        return $this;
    }

    /**
     * Use sequence name for store upload file.
     *
     * @return $this
     */
    public function sequenceName()
    {
        $this->useSequenceName = true;

        return $this;
    }

    /**
     * Generate a unique name for uploaded file.
     *
     * @param UploadedFile $file
     *
     * @return string
     */
    protected function generateUniqueName(UploadedFile $file)
    {
        return md5(uniqid()).'.'.$file->getClientOriginalExtension();
    }

    /**
     * Generate a sequence name for uploaded file.
     *
     * @param UploadedFile $file
     *
     * @return string
     */
    protected function generateSequenceName(UploadedFile $file)
    {
        $index = 1;
        $extension = $file->getClientOriginalExtension();
        $originalName = $file->getClientOriginalName();
        $newName = $originalName.'_'.$index.'.'.$extension;

        while ($this->getStorage()->exists("{$this->getDirectory()}/$newName")) {
            $index++;
            $newName = $originalName.'_'.$index.'.'.$extension;
        }

        return $newName;
    }

    /**
     * @param UploadedFile $file
     *
     * @return bool|\Illuminate\Support\MessageBag
     */
    protected function getErrorMessages(UploadedFile $file)
    {
        $rules = $attributes = [];

        if (! $fieldRules = $this->getRules()) {
            return false;
        }

        $rules[$this->column] = $fieldRules;
        $attributes[$this->column] = $this->label;

        /* @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make([$this->column => $file], $rules, $this->validationMessages, $attributes);

        if (! $validator->passes()) {
            $errors = $validator->errors()->getMessages()[$this->column];

            return implode('; ', $errors);
        }
    }

    /**
     * Destroy original files.
     *
     * @return void.
     */
    public function destroy()
    {
        if ($this->retainable) {
            return;
        }

        $this->deleteFile($this->original);
    }

    /**
     * Destroy original files.
     *
     * @param $file
     */
    public function destroyIfChanged($file)
    {
        if (! $file || ! $this->original) {
            return $this->destroy();
        }

        $file = array_filter((array) $file);
        $original = (array) $this->original;

        $this->deleteFile(Arr::except(array_combine($original, $original), $file));
    }

    /**
     * Destroy files.
     *
     * @param $path
     */
    public function deleteFile($path)
    {
        if (! $path) {
            return;
        }

        if (is_array($path)) {
            foreach ($path as $v) {
                $this->deleteFile($v);
            }

            return;
        }

        $storage = $this->getStorage();

        if ($storage->exists($path)) {
            $storage->delete($path);
        } else {
            $prefix = $storage->url('');
            $path = str_replace($prefix, '', $path);

            if ($storage->exists($path)) {
                $storage->delete($path);
            }
        }
    }

    /**
     * Get storage instance.
     *
     * @return \Illuminate\Filesystem\Filesystem|null
     */
    public function getStorage()
    {
        if ($this->storage === null) {
            $this->initStorage();
        }

        return $this->storage;
    }

    /**
     * Set disk for storage.
     *
     * @param string $disk Disks defined in `config/filesystems.php`.
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function disk($disk)
    {
        try {
            $this->storage = Storage::disk($disk);
        } catch (\Exception $exception) {
            if (! array_key_exists($disk, config('filesystems.disks'))) {
                admin_error(
                    'Config error.',
                    "Disk [$disk] not configured, please add a disk config in `config/filesystems.php`."
                );

                return $this;
            }

            throw $exception;
        }

        return $this;
    }

    /**
     * Get file visit url.
     *
     * @param string $path
     *
     * @return string
     */
    public function objectUrl($path)
    {
        if (URL::isValidUrl($path)) {
            return $path;
        }

        return $this->getStorage()->url($path);
    }

    /**
     * @param $permission
     *
     * @return $this
     */
    public function storagePermission($permission)
    {
        $this->storagePermission = $permission;

        return $this;
    }
}
