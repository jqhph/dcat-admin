<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;
use Dcat\Admin\Form\Field;
use Illuminate\Support\Arr;

class KeyValue extends Field
{
    const DEFAULT_FLAG_NAME = '_def_';

    /**
     * @var array
     */
    protected $value = ['' => ''];

    /**
     * Fill data to the field.
     *
     * @param array $data
     *
     * @return mixed
     */
    public function formatFieldData($data)
    {
        $this->data = $data;

        return Arr::get($data, $this->column, $this->value);
    }

    /**
     * {@inheritdoc}
     */
    public function getValidator(array $input)
    {
        if ($this->validator) {
            return $this->validator->call($this, $input);
        }

        if (! is_string($this->column)) {
            return false;
        }

        $rules = $attributes = [];

        if (! $fieldRules = $this->getRules()) {
            return false;
        }

        if (! Arr::has($input, $this->column)) {
            return false;
        }

        $rules["{$this->column}.keys.*"] = 'distinct';
        $rules["{$this->column}.values.*"] = $fieldRules;
        $attributes["{$this->column}.keys.*"] = __('Key');
        $attributes["{$this->column}.values.*"] = __('Value');

        $input = $this->prepareValidatorInput($input);

        return validator($input, $rules, $this->getValidationMessages(), $attributes);
    }

    protected function prepareValidatorInput(array $input)
    {
        Arr::forget($input, $this->column.'.'.static::DEFAULT_FLAG_NAME);

        return $input;
    }

    protected function setupScript()
    {
        $value = old($this->column, $this->value());

        $number = $value ? count($value) : 0;
        $class = $this->elementClassString();

        $this->script = <<<JS
(function () {
    var index = {$number};
    $('.{$class}-add').on('click', function () {
        var tpl = $('template.{$class}-tpl').html().replace('{key}', index).replace('{key}', index);
        $('tbody.kv-{$class}-table').append(tpl);
        
        index++;
    });
    
    $('tbody').on('click', '.{$class}-remove', function () {
        $(this).closest('tr').remove();
    });
})();
JS;
    }

    protected function prepareToSave($value)
    {
        unset($value[static::DEFAULT_FLAG_NAME]);

        if (empty($value)) {
            return [];
        }

        return array_combine($value['keys'], $value['values']);
    }

    public function render()
    {
        $this->setupScript();

        Admin::style('td .form-group {margin-bottom: 0 !important;}');

        return parent::render();
    }
}
