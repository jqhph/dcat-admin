<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form;
use Dcat\Admin\Support\WebUploader as WebUploaderHelper;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * @property Form $form
 */
trait WebUploader
{
    /**
     * @param  string  $extensions  exp. gif,jpg,jpeg,bmp,png
     * @param  string|null  $mimeTypes  exp. image/*
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
     * @param  string  $mimeTypes  exp. image/*
     * @return $this
     */
    public function mimeTypes(string $mimeTypes)
    {
        $this->options['accept']['mimeTypes'] = $mimeTypes;

        return $this;
    }

    /**
     * @param  bool  $value
     * @return $this
     */
    public function chunked(bool $value = true)
    {
        $this->options['chunked'] = $value;

        return $this;
    }

    /**
     * @param  int|null  $size  kb
     * @return $this
     */
    public function chunkSize(int $size)
    {
        $this->options['chunkSize'] = $size * 1024;

        $this->chunked(true);

        return $this;
    }

    /**
     * @param  int  $size  kb
     * @return $this
     */
    public function maxSize(int $size)
    {
        $this->rules('max:'.$size);
        $this->options['fileSingleSizeLimit'] = $size * 1024;

        return $this;
    }

    /**
     * @param  int  $num
     * @return $this
     */
    public function threads(int $num)
    {
        $this->options['threads'] = $num;

        return $this;
    }

    /**
     * 设置上传接口.
     *
     * @param  string  $server
     * @return $this
     */
    public function url(string $server)
    {
        $this->options['server'] = admin_url($server);

        $this->deleteUrl($server);

        return $this;
    }

    /**
     * 禁止上传文件后自动更新字段值.
     *
     * @param  bool  $value
     * @return $this
     */
    public function autoSave(bool $value = true)
    {
        $this->options['autoUpdateColumn'] = $value;

        return $this;
    }

    /**
     * 禁用前端删除功能.
     *
     * @param  bool  $value
     * @return $this
     */
    public function removable(bool $value = true)
    {
        $this->options['removable'] = ! $value;

        return $this;
    }

    /**
     * 设置图片删除地址.
     *
     * @param  string  $server
     * @return $this
     */
    public function deleteUrl(string $server)
    {
        $this->options['deleteUrl'] = admin_url($server);

        return $this;
    }

    /**
     * 设置上传表单请求参数.
     *
     * @param  array  $data
     * @return $this
     */
    public function withFormData(array $data)
    {
        $this->options['formData'] = array_merge($this->options['formData'], $data);

        return $this;
    }

    /**
     * 设置删除图片请求参数.
     *
     * @param  array  $data
     * @return $this
     */
    public function withDeleteData(array $data)
    {
        $this->options['deleteData'] = array_merge($this->options['deleteData'], $data);

        return $this;
    }

    /**
     * 是否开启自动上传.
     *
     * @param  bool  $value
     * @return $this
     */
    public function autoUpload(bool $value = true)
    {
        $this->options['autoUpload'] = $value;

        return $this;
    }

    /**
     * 是否开启图片压缩.
     *
     * @param  bool|array  $compress
     * @return $this
     */
    public function compress($compress = true)
    {
        $this->options['compress'] = $compress;

        return $this;
    }

    /**
     * 是否允许下载文件.
     *
     * @param  bool  $value
     * @return $this
     */
    public function downloadable(bool $value = true)
    {
        $this->options['downloadable'] = $value;

        return $this;
    }

    /**
     * 默认上传配置.
     *
     * @return void
     */
    protected function setUpDefaultOptions()
    {
        $key = optional($this->form)->getKey();

        $defaultOptions = [
            'name'                => WebUploaderHelper::FILE_NAME,
            'fileVal'             => WebUploaderHelper::FILE_NAME,
            'isImage'             => false,
            'removable'           => false,
            'chunked'             => false,
            'fileNumLimit'        => 10,
            // 禁掉全局的拖拽功能。这样不会出现图片拖进页面的时候，把图片打开。
            'disableGlobalDnd'    => true,
            'fileSizeLimit'       => 20971520000, // 20000M
            'fileSingleSizeLimit' => 10485760, // 10M
            'elementName'         => $this->getElementName(), // 字段name属性值
            'lang'                => trans('admin.uploader'),
            'compress'            => false,
            'accept'              => [],
            'deleteData' => [
                static::FILE_DELETE_FLAG => '',
                'primary_key'            => $key,
            ],
            'formData' => [
                '_id'           => Str::random(),
                '_token'        => csrf_token(),
                'upload_column' => $this->column(),
                'primary_key'   => $key,
            ],
        ];

        $this->options($this->options += $defaultOptions);
    }

    protected function setDefaultServer()
    {
        if (! $this->form || ! method_exists($this->form, 'action')) {
            return;
        }

        if (empty($this->options['server'])) {
            $this->options['server'] = $this->form->action();
        }
        if (empty($this->options['updateServer'])) {
            $this->options['updateServer'] = $this->form->action();
        }
        if (empty($this->options['deleteUrl'])) {
            $this->options['deleteUrl'] = $this->form->action();
        }

        if (
            method_exists($this->form, 'builder')
            && $this->form->builder()
            && $this->form->builder()->isEditing()
        ) {
            $this->options['formData']['_method'] = 'PUT';
            $this->options['deleteData']['_method'] = 'PUT';
            if (! isset($this->options['autoUpdateColumn'])) {
                $this->options['autoUpdateColumn'] = true;
            }
        }
    }

    /**
     * 获取创建链接.
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return str_replace('/create', '', URL::full());
    }

    /**
     * 图片预览设置.
     *
     * @return void
     */
    protected function setupPreviewOptions()
    {
        $this->options['preview'] = $this->initialPreviewConfig();
    }
}
