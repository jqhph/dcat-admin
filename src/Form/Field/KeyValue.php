<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;
use Dcat\Admin\Form\Field;
use Illuminate\Support\Arr;

class KeyValue extends Field
{
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

        if (!is_string($this->column)) {
            return false;
        }

        $rules = $attributes = [];

        if (!$fieldRules = $this->getRules()) {
            return false;
        }

        if (!Arr::has($input, $this->column)) {
            return false;
        }

        $rules["{$this->column}.keys.*"] = 'distinct';
        $rules["{$this->column}.values.*"] = $fieldRules;
        $attributes["{$this->column}.keys.*"] = __('Key');
        $attributes["{$this->column}.values.*"] = __('Value');

        return validator($input, $rules, $this->getValidationMessages(), $attributes);
    }

    protected function setupScript()
    {
        $this->script = <<<JS

$('.{$this->column}-add').on('click', function () {
    var tpl = $('template.{$this->column}-tpl').html();
    $('tbody.kv-{$this->column}-table').append(tpl);
});

$('tbody').on('click', '.{$this->column}-remove', function () {
    $(this).closest('tr').remove();
});

JS;
    }

    public function prepare($value)
    {
        return array_combine($value['keys'], $value['values']);
    }

    public function render()
    {
        $this->setupScript();

        Admin::style('td .form-group {margin-bottom: 0 !important;}');

        return parent::render();
    }
}
