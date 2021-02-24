<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Exception\UploadException;
use Dcat\Admin\Traits\HasUploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

trait UploadField
{
    use HasUploadedFile {
        disk as _disk;
    }

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
     * @var bool
     */
    protected $saveFullUrl = false;

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
            $this->name = $this->name->call($this->values(), $file);
        }

        if ($this->name !== '' && is_string($this->name)) {
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
            $this->directory = $this->directory->call($this->values(), $this->form);
        }

        return $this->directory ?: $this->defaultDirectory();
    }

    /**
     * Indicates if the underlying field is retainable.
     *
     * @param bool $retainable
     *
     * @return $this
     */
    public function retainable(bool $retainable = true)
    {
        $this->retainable = $retainable;

        return $this;
    }

    public function saveFullUrl(bool $value = true)
    {
        $this->saveFullUrl = $value;

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
        $request = request();

        $id = $request->get('_id');

        if (! $id) {
            return $this->responseErrorMessage('Missing id');
        }

        if ($errors = $this->getValidationErrors($file)) {
            return $this->responseValidationMessage($errors);
        }

        $this->name = $this->getStoreName($file);

        $this->renameIfExists($file);

        $this->prepareFile($file);

        if (! is_null($this->storagePermission)) {
            $result = $this->getStorage()->putFileAs($this->getDirectory(), $file, $this->name, $this->storagePermission);
        } else {
            $result = $this->getStorage()->putFileAs($this->getDirectory(), $file, $this->name);
        }

        if ($result) {
            $path = $this->getUploadPath();
            $url = $this->objectUrl($path);

            // 上传成功
            return $this->responseUploaded($this->saveFullUrl ? $url : $path, $url);
        }

        // 上传失败
        throw new UploadException(trans('admin.uploader.upload_failed'));
    }

    /**
     * @param UploadedFile $file
     */
    protected function prepareFile(UploadedFile $file)
    {
    }

    /**
     * Specify the directory and name for upload file.
     *
     * @param string|\Closure $directory
     * @param null|string     $name
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
     * @param string|\Closure $dir
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
    protected function getValidationErrors(UploadedFile $file)
    {
        $rules = $attributes = [];

        // 如果文件上传有错误，则直接返回错误信息
        if ($file->getError() !== UPLOAD_ERR_OK) {
            return $file->getErrorMessage();
        }

        if (! $fieldRules = $this->getRules()) {
            return false;
        }

        $rules[$this->column] = $fieldRules;
        $attributes[$this->column] = $this->label;

        /* @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make([$this->column => $file], $rules, $this->validationMessages, $attributes);

        if (! $validator->passes()) {
            $errors = $validator->errors()->getMessages()[$this->column];

            return implode('<br> ', $errors);
        }
    }

    /**
     * Destroy original files.
     *
     * @return void.
     */
    public function destroy()
    {
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
     * @param string|array $path
     */
    public function deleteFile($paths)
    {
        if (! $paths || $this->retainable) {
            return;
        }

        $storage = $this->getStorage();

        foreach ((array) $paths as $path) {
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
