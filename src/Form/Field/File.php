<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Contracts\UploadField as UploadFieldInterface;
use Dcat\Admin\Form\Field;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Support\JavaScript;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class File extends Field implements UploadFieldInterface
{
    use WebUploader, UploadField;

    protected static $css = [
        '@webuploader',
    ];

    protected static $js = [
        '@webuploader',
    ];

    protected $containerId;

    protected $relationName;

    /**
     * @param string $column
     * @param array  $arguments
     */
    public function __construct($column, $arguments = [])
    {
        parent::__construct($column, $arguments);

        $this->setupDefaultOptions();

        $this->containerId = $this->generateId();
    }

    /**
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

        if (! Arr::has($input, $this->column)) {
            return false;
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
     * @param string $file
     *
     * @return mixed|string
     */
    protected function prepareInputValue($file)
    {
        if (request()->has(static::FILE_DELETE_FLAG)) {
            return $this->destroy();
        }

        $this->destroyIfChanged($file);

        return $file;
    }

    /**
     * 设置字段的关联关系（在一/多对多表单中使用）.
     *
     * @param string|null $name
     *
     * @return $this
     */
    public function setRelation(?string $name, $key)
    {
        $this->relationName = $name;
        $this->options['formData']['_relation'] = [$name, $key];

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRelation()
    {
        return $this->relationName;
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
     * @return string
     */
    public function render()
    {
        $this->setDefaultServer();

        if (! empty($this->value())) {
            $this->setupPreviewOptions();
        }

        $this->forceOptions();
        $this->formatValue();
        $this->setupScript();

        $this->addVariables([
            'fileType'    => $this->options['isImage'] ? '' : 'file',
            'containerId' => $this->containerId,
        ]);

        return parent::render();
    }

    protected function setupScript()
    {
        $newButton = trans('admin.uploader.add_new_media');
        $options = JavaScript::format($this->options);

        $this->script = <<<JS
(function () {
    var uploader, newPage, options = {$options};

    build();

    function build() {
        var opts = $.extend({
            selector: '#{$this->containerId}',
            addFileButton: '#{$this->containerId} .add-file-button',
            inputSelector: '#{$this->id}',
        }, options);

        opts.upload = $.extend({
            pick: {
                id: '#{$this->containerId} .file-picker',
                name: '_file_',
                label: '<i class="feather icon-folder"></i>&nbsp; {$newButton}'
            },
            dnd: '#{$this->containerId} .dnd-area',
            paste: '#{$this->containerId} .web-uploader'
        }, opts);

        uploader = Dcat.Uploader(opts);
        uploader.build();
        uploader.preview();

        function resize() {
            setTimeout(function () {
                if (! uploader) return;

                uploader.refreshButton();
                resize();

                if (! newPage) {
                    newPage = 1;
                    $(document).one('pjax:complete', function () {
                        uploader = null;
                    });
                }
            }, 250);
        }
        resize();
        
        $('[name="file-{$this->getElementName()}"]').change(function () {
            uploader.uploader.addFiles(this.files);
        });
    }
})();
JS;
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
