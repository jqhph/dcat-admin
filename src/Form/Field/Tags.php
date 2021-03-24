<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form\Field;
use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Tags extends Field
{
    /**
     * @var array
     */
    protected $value = [];

    /**
     * @var bool
     */
    protected $keyAsValue = false;

    /**
     * @var string
     */
    protected $visibleColumn = null;

    /**
     * @var string
     */
    protected $key = null;

    /**
     * @var string
     */
    protected $ajaxScript = null;

    /**
     * {@inheritdoc}
     */
    protected function formatFieldData($data)
    {
        $value = $this->getValueFromData($data);

        if (is_array($value) && $this->keyAsValue) {
            $value = array_column($value, $this->visibleColumn, $this->key);
        }

        return Helper::array($value);
    }

    /**
     * Set visible column and key of data.
     *
     * @param $visibleColumn
     * @param $key
     *
     * @return $this
     */
    public function pluck($visibleColumn, $key = 'id')
    {
        if (! empty($visibleColumn) && ! empty($key)) {
            $this->keyAsValue = true;
        }

        $this->visibleColumn = $visibleColumn;
        $this->key = $key;

        return $this;
    }

    /**
     * Sanitize input data.
     *
     * @param array  $input
     * @param string $column
     *
     * @return array
     */
    protected function sanitizeInput($input, $column)
    {
        $input = parent::sanitizeInput($input, $column);

        $value = array_filter((array) Arr::get($input, $this->column), function ($value) {
            return $value !== null;
        });

        Arr::set($input, $this->column, $value);

        return $input;
    }

    /**
     * Set the field options.
     *
     * @param array|Collection|Arrayable|\Closure $options
     *
     * @return $this|Field
     */
    public function options($options = [])
    {
        if ($options instanceof \Closure) {
            $this->options = $options;

            return $this;
        }

        if (! $this->keyAsValue) {
            return parent::options($options);
        }

        if ($options instanceof Collection) {
            $options = $options->pluck($this->visibleColumn, $this->key)->toArray();
        }

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        $this->options = $options + $this->options;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareInputValue($value)
    {
        if (! is_array($value)) {
            return $value;
        }

        return array_filter($value, 'strlen');
    }

    /**
     * Get or set value for this field.
     *
     * @param mixed $value
     *
     * @return $this|array|mixed
     */
    public function value($value = null)
    {
        if (is_null($value)) {
            return Helper::array(parent::value());
        }

        $this->value = Helper::array($value);

        return $this;
    }

    /**
     * Load options from ajax results.
     *
     * @param string $url
     * @param $idField
     * @param $textField
     *
     * @return $this
     */
    public function ajax(string $url, string $idField = 'id', string $textField = 'text')
    {
        $url = admin_url($url);

        return $this->addVariables(['ajax' => compact('url', 'idField', 'textField')]);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $value = Helper::array($this->value());

        if ($this->options instanceof \Closure) {
            $this->options(
                $this->options->call($this->values(), $value, $this)
            );
        }

        if ($this->keyAsValue) {
            $options = $value + $this->options;
        } else {
            $options = array_unique(array_merge($value, (array) $this->options));
        }

        $this->addVariables([
            'options'    => $options,
            'keyAsValue' => $this->keyAsValue,
        ]);

        return parent::render();
    }
}
