<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Contracts\UploadField as UploadFieldInterface;
use Dcat\Admin\Form\Field;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Support\JavaScript;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class File extends Field implements UploadFieldInterface
{
    use WebUploader;
    use UploadField;

    /**
     * @var array
     */
    protected $options = [
        'events' => [],
        'override' => false,
    ];

    public function __construct($column, $arguments = [])
    {
        parent::__construct($column, $arguments);

        $this->setUpDefaultOptions();
    }

    public function setElementName($name)
    {
        $this->mergeOptions(['elementName' => $name]);

        return parent::setElementName($name);
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
        $requiredIf = null;

        if (! $this->hasRule('required') && ! $requiredIf = $this->getRule('required_if*')) {
            return false;
        }

        $rules[$this->column] = $requiredIf ?: 'required';
        $attributes[$this->column] = $this->label;

        return Validator::make($input, $rules, $this->getValidationMessages(), $attributes);
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function setRelation(array $options = [])
    {
        $this->options['formData']['_relation'] = [$options['relation'], $options['key'] ?? null];

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function disable(bool $value = true)
    {
        $this->options['disabled'] = $value;

        return $this;
    }

    protected function formatFieldData($data)
    {
        return Helper::array($this->getValueFromData($data));
    }

    /**
     * @return array
     */
    protected function initialPreviewConfig()
    {
        $previews = [];

        foreach (Helper::array($this->value()) as $value) {
            $previews[] = [
                'id'   => $value,
                'path' => Helper::basename($value),
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
     * {@inheritDoc}
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
            'fileType'      => $this->options['isImage'] ? '' : 'file',
            'showUploadBtn' => ($this->options['autoUpload'] ?? false) ? false : true,
            'options'       => JavaScript::format($this->options),
        ]);

        return parent::render();
    }

    /**
     * @return void
     */
    protected function formatValue()
    {
        if ($this->value !== null) {
            $this->value = implode(',', Helper::array($this->value));
        } elseif (is_array($this->default)) {
            $this->default = implode(',', $this->default);
        }
    }

    /**
     * Webuploader 事件监听.
     *
     * @see http://fex.baidu.com/webuploader/doc/index.html#WebUploader_Uploader_events
     *
     * @param  string  $event
     * @param  string  $script
     * @param  bool  $once
     * @return $this
     */
    public function on(string $event, string $script, bool $once = false)
    {
        $script = JavaScript::make($script);

        $this->options['events'][] = compact('event', 'script', 'once');

        return $this;
    }

    /**
     * Webuploader 事件监听(once).
     *
     * @see http://fex.baidu.com/webuploader/doc/index.html#WebUploader_Uploader_events
     *
     * @param  string  $event
     * @param  string  $script
     * @return $this
     */
    public function once(string $event, string $script)
    {
        return $this->on($event, $script, true);
    }

    /**
     * @param  Field  $field
     * @param  string|array  $fieldRules
     * @return void
     */
    public static function deleteRules(Field $field, &$fieldRules)
    {
        if ($field instanceof self) {
            $fieldRules = is_string($fieldRules) ? explode('|', $fieldRules) : $fieldRules;

            Helper::deleteContains($fieldRules, ['image', 'file', 'dimensions', 'size', 'max', 'min']);
        }
    }

    public function override(bool $override = true)
    {
        $this->options['override'] = $override;

        return $this;
    }
}
