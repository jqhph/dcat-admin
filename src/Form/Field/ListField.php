<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Support\Helper;
use Illuminate\Support\Arr;
use Illuminate\Support\MessageBag;

class ListField extends Field
{
    const DEFAULT_FLAG_NAME = '_def_';

    /**
     * Max list size.
     *
     * @var int
     */
    protected $max;

    /**
     * Minimum list size.
     *
     * @var int
     */
    protected $min = 0;

    /**
     * Set Max list size.
     *
     * @param int $size
     *
     * @return $this
     */
    public function max(int $size)
    {
        $this->max = $size;

        return $this;
    }

    /**
     * Set Minimum list size.
     *
     * @param int $size
     *
     * @return $this
     */
    public function min(int $size)
    {
        $this->min = $size;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function formatFieldData($data)
    {
        $this->data = $data;

        $value = Helper::array($this->getValueFromData($data, null, $this->value));

        unset($value['values'][static::DEFAULT_FLAG_NAME]);

        return $value;
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
        if (
            (! $fieldRules = $this->getRules())
            && ! $this->max
            && ! $this->min
        ) {
            return false;
        }

        if (! Arr::has($input, $this->column)) {
            return false;
        }

        if ($fieldRules) {
            $rules["{$this->column}.values.*"] = $fieldRules;
        }
        $attributes["{$this->column}.values.*"] = __('Value');
        $rules["{$this->column}.values"][] = 'array';

        if (! is_null($this->max)) {
            $rules["{$this->column}.values"][] = "max:$this->max";
        }

        if (! is_null($this->min)) {
            $rules["{$this->column}.values"][] = "min:$this->min";
        }

        $attributes["{$this->column}.values"] = $this->label;

        $input = $this->prepareValidatorInput($input);

        return validator($input, $rules, $this->getValidationMessages(), $attributes);
    }

    public function formatValidatorMessages($messageBag)
    {
        $messages = new MessageBag();

        foreach ($messageBag->toArray() as $column => $message) {
            $messages->add($this->column, $message);
        }

        return $messages;
    }

    protected function prepareValidatorInput(array $input)
    {
        Arr::forget($input, "{$this->column}.values.".static::DEFAULT_FLAG_NAME);

        return $input;
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareInputValue($value)
    {
        unset($value['values'][static::DEFAULT_FLAG_NAME]);

        if (empty($value['values'])) {
            return [];
        }

        return array_values($value['values']);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $value = $this->value();

        $this->addVariables(['count' => $value ? count($value) : 0]);

        return parent::render();
    }
}
