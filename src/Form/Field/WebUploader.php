<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * @property Form $form
 */
trait WebUploader
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param string      $extensions exp. gif,jpg,jpeg,bmp,png
     * @param string|null $mimeTypes  exp. image/*
     *
     * @return $this
     */
    public function accept(string $extensions, string $mimeTypes = null)
    {
        $this->options['accept'] = [
            'extensions' => $extensions,
        ];

        if ($mimeTypes !== null) {
            $this->options['accept']['mimeTypes'] = $mimeTypes;
        }

        return $this;
    }

    /**
     * @param bool $disable
     *
     * @return $this
     */
    public function disableChunked(bool $disable = true)
    {
        $this->options['chunked'] = ! $disable;

        return $this;
    }

    /**
     * @param int|null $size kb
     *
     * @return $this
     */
    public function chunkSize(int $size)
    {
        $this->options['chunkSize'] = $size * 1024;

        return $this;
    }

    /**
     * @param int $size kb
     *
     * @return $this
     */
    public function maxSize(int $size)
    {
        $this->rules('max:'.$size);
        $this->options['fileSingleSizeLimit'] = $size * 1024;

        return $this;
    }

    /**
     * @param int $num
     *
     * @return $this
     */
    public function threads(int $num)
    {
        $this->options['threads'] = $num;

        return $this;
    }

    /**
     * Set upload server.
     *
     * @param string $server
     *
     * @return $this
     */
    public function url(string $server)
    {
        $this->options['server'] = admin_url($server);

        return $this;
    }

    /**
     * Disable auto save the path of uploaded file.
     *
     * @return $this
     */
    public function disableAutoSave()
    {
        $this->options['autoUpdateColumn'] = false;

        return $this;
    }

    /**
     * Disable remove file.
     *
     * @return $this
     */
    public function disableRemove()
    {
        $this->options['disableRemove'] = true;

        return $this;
    }

    /**
     * Set upload server.
     *
     * @param string $server
     *
     * @return $this
     */
    public function deleteUrl(string $server)
    {
        $this->options['deleteUrl'] = admin_url($server);

        return $this;
    }

    /**
     * Set default options form file field.
     *
     * @return void
     */
    protected function setupDefaultOptions()
    {
        $defaultOptions = [
            'isImage'             => false,
            'disableRemove'       => false,
            'chunked'             => true,
            'fileNumLimit'        => 10,
            // 禁掉全局的拖拽功能。这样不会出现图片拖进页面的时候，把图片打开。
            'disableGlobalDnd'    => true,
            'fileSizeLimit'       => 20971520000, // 20000M
            'fileSingleSizeLimit' => 10485760, // 10M
            'autoUpdateColumn'    => true, // 上传完图片后自动保存图片路径
            'elementName'         => $this->elementName(), // 字段name属性值
            'lang'                => trans('admin.uploader'),

            'deleteData' => [
                static::FILE_DELETE_FLAG => '',
                '_token'                 => csrf_token(),
                '_method'                => 'PUT',
            ],
            'formData' => [
                '_id'           => Str::random(),
                'upload_column' => $this->column,
                '_method'       => 'PUT',
                '_token'        => csrf_token(),
            ],
        ];

        $this->options($defaultOptions);
    }

    protected function setDefaultServer()
    {
        if (! $this->form || ! method_exists($this->form, 'action')) {
            return;
        }

        if (empty($this->options['server'])) {
            $this->options['server'] = $this->form->action();
        }
        if (empty($this->options['deleteUrl'])) {
            $this->options['deleteUrl'] = $this->form->action();
        }

        if (
            method_exists($this->form, 'builder')
            && $this->form->builder()
            && $this->form->builder()->isCreating()
        ) {
            unset(
                $this->options['formData']['_method'],
                $this->options['deleteData']['_method'],
                $this->options['autoUpdateColumn']
            );
        }
    }

    /**
     * Get create url.
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return str_replace('/create', '', URL::full());
    }

    /**
     * Set preview options form image field.
     *
     * @return void
     */
    protected function setupPreviewOptions()
    {
        $this->options['preview'] = $this->initialPreviewConfig();
    }

    /**
     * Set options for file-upload plugin.
     *
     * @param array $options
     *
     * @return $this
     */
    public function options($options = [])
    {
        $this->options = array_merge($options, $this->options);

        return $this;
    }
}
