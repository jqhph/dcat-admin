<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;
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
     * @var \Closure
     */
    protected $saveAction = null;

    /**
     * {@inheritdoc}
     */
    protected function formatFieldData($data)
    {
        $value = Arr::get($data, $this->column);

        if (is_array($value) && $this->keyAsValue) {
            $value = array_column($value, $this->visibleColumn, $this->key);
        }

        return array_filter(Helper::array($value), 'strlen');
    }

    /**
     * Set visible column and key of data.
     *
     * @param $visibleColumn
     * @param $key
     *
     * @return $this
     */
    public function pluck($visibleColumn, $key)
    {
        if (!empty($visibleColumn) && !empty($key)) {
            $this->keyAsValue = true;
        }

        $this->visibleColumn = $visibleColumn;
        $this->key = $key;

        return $this;
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
        if ($this->options instanceof \Closure) {
            $this->options = $options;

            return $this;
        }

        if (!$this->keyAsValue) {
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
     * Set save Action.
     *
     * @param \Closure $saveAction
     *
     * @return $this
     */
    public function saving(\Closure $saveAction)
    {
        $this->saveAction = $saveAction;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare($value)
    {
        $value = array_filter($value, 'strlen');

        if ($this->keyAsValue) {
            return is_null($this->saveAction) ? $value : ($this->saveAction)($value);
        }

        if (is_array($value) && !Arr::isAssoc($value)) {
            $value = implode(',', $value);
        }

        return $value;
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
            return empty($this->value) ? ($this->getDefault() ?? []) : $this->value;
        }

        $this->value = (array) $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        if ($this->options instanceof \Closure) {
            $this->options(
                $this->options->call($this->getFormModel(), $this->value, $this)
            );
        }

        $this->script = "$(\"{$this->getElementClassSelector()}\").select2({
            tags: true,
            tokenSeparators: [',']
        });";

        if ($this->keyAsValue) {
            $options = $this->value + $this->options;
        } else {
            $options = array_unique(array_merge($this->value, $this->options));
        }

        return parent::render()->with([
            'options'    => $options,
            'keyAsValue' => $this->keyAsValue,
        ]);
    }

    public static function collectAssets()
    {
        Admin::collectComponentAssets('select2');
    }
}
