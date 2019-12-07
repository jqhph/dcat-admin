<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Support\Helper;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class File extends Field
{
    use WebUploader, UploadField;

    /**
     * Css.
     *
     * @var array
     */
    protected static $css = [
        'vendor/dcat-admin/webuploader/webuploader.min.css',
    ];

    /**
     * Js.
     *
     * @var array
     */
    protected static $js = [
        'vendor/dcat-admin/webuploader/webuploader.min.js',
        'vendor/dcat-admin/dcat-admin/upload.min.js',
    ];

    /**
     * Create a new File instance.
     *
     * @param string $column
     * @param array  $arguments
     */
    public function __construct($column, $arguments = [])
    {
        parent::__construct($column, $arguments);

        $this->setupDefaultOptions();
    }

    /**
     * Default directory for file to upload.
     *
     * @return mixed
     */
    public function defaultDirectory()
    {
        return config('admin.upload.directory.file');
    }

    /**
     * {@inheritdoc}
     */
    public function getValidator(array $input)
    {
        if (request()->has(static::FILE_DELETE_FLAG)) {
            return false;
        }

        if ($this->validator) {
            return $this->validator->call($this, $input);
        }

        $value = Arr::get($input, $this->column);
        $value = array_filter(is_array($value) ? $value : explode(',', $value));

        $fileLimit = $this->options['fileNumLimit'] ?? 1;
        if ($fileLimit < count($value)) {
            $this->form->responseValidationMessages(
                $this->column,
                trans('admin.uploader.max_file_limit', ['attribute' => $this->label, 'max' => $fileLimit])
            );

            return false;
        }

        $rules = $attributes = [];

        if (! $this->hasRule('required')) {
            return false;
        }

        $rules[$this->column] = 'required';
        $attributes[$this->column] = $this->label;

        return Validator::make($input, $rules, $this->getValidationMessages(), $attributes);
    }

    /**
     * Prepare for saving.
     *
     * @param string $file
     *
     * @return mixed|string
     */
    protected function prepareToSave($file)
    {
        if (request()->has(static::FILE_DELETE_FLAG)) {
            return $this->destroy();
        }

        $this->destroyIfChanged($file);

        return $file;
    }

    /**
     * Set field as disabled.
     *
     * @return $this
     */
    public function disable()
    {
        $this->options['disabled'] = true;

        return $this;
    }

    protected function formatFieldData($data)
    {
        return Helper::array(Arr::get($data, $this->column));
    }

    /**
     * @return array
     */
    protected function initialPreviewConfig()
    {
        $previews = [];

        foreach ($this->value() as $value) {
            $previews[] = [
                'id'   => $value,
                'path' => basename($value),
                'url'  => $this->objectUrl($value),
            ];
        }

        return $previews;
    }

    protected function forceOptions()
    {
        $this->options['fileNumLimit'] = 1;
    }

    /**
     * Render file upload field.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        $this->setDefaultServer();

        if (! empty($this->value())) {
            $this->setupPreviewOptions();
        }

        $this->forceOptions();

        $this->formatValue();

        $this->addVariables([
            'options' => json_encode($this->options),
            '_files'  => $this->options['isImage'] ? '' : '_files',
            '_id'     => $this->generateId(),
        ]);

        return parent::render();
    }

    /**
     * @return void
     */
    protected function formatValue()
    {
        if ($this->value !== null) {
            $this->value = implode(',', $this->value);
        } elseif (is_array($this->default)) {
            $this->default = implode(',', $this->default);
        }
    }

    /**
     * @return string
     */
    protected function generateId()
    {
        return 'file-'.Str::random(8);
    }
}
