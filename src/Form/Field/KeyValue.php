<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Support\Helper;
use Illuminate\Support\Arr;

class KeyValue extends Field
{
    const DEFAULT_FLAG_NAME = '_def_';

    /**
     * {@inheritdoc}
     */
    public function formatFieldData($data)
    {
        $this->data = $data;

        return Helper::array(Arr::get($data, $this->normalizeColumn(), $this->value));
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

    protected function prepareInputValue($value)
    {
        unset($value[static::DEFAULT_FLAG_NAME]);

        if (empty($value)) {
            return [];
        }

        return array_combine($value['keys'], $value['values']);
    }

    public function render()
    {
        $value = $this->value();

        $this->addVariables([
            'count' => $value ? count($value) : 0,
        ]);

        return parent::render();
    }
}
