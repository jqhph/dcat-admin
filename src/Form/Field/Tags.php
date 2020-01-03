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
     * {@inheritdoc}
     */
    protected function formatFieldData($data)
    {
        $value = Arr::get($data, $this->column);

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
    public function pluck($visibleColumn, $key)
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
    protected function prepareToSave($value)
    {
        if (! is_array($value)) {
            return $value;
        }

        $value = array_filter($value, 'strlen');

        if ($value && ! Arr::isAssoc($value)) {
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
            return empty($this->value) ? Helper::array($this->default()) : $this->value;
        }

        $this->value = Helper::array($value);

        return $this;
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

        $this->setupScript();

        if ($this->keyAsValue) {
            $options = $value + $this->options;
        } else {
            $options = array_unique(array_merge($value, $this->options));
        }

        return parent::render()->with([
            'options'    => $options,
            'keyAsValue' => $this->keyAsValue,
        ]);
    }

    protected function setupScript()
    {
        // 解决部分浏览器开启 tags: true 后无法输入中文的BUG
        // 支持【逗号】【分号】【空格】结尾生成tags
        $this->script = <<<JS
$("{$this->elementClassSelector()}").select2({
    tags: true,
    tokenSeparators: [',', ';', '，', '；', ' '],
    createTag: function(params) {
        if (/[,;，； ]/.test(params.term)) {
            var str = params.term.trim().replace(/[,;，；]*$/, '');
            return { id: str, text: str }
        } else {
            return null;
        }
    }
});
JS;

        // 解决输入中文后无法回车结束的问题。
        Admin::script(
            <<<'JS'
$(document).off('keyup', '.select2-selection--multiple .select2-search__field').on('keyup', '.select2-selection--multiple .select2-search__field', function (event) {
    try {
        if (event.keyCode == 13) {
            var $this = $(this), optionText = $this.val();
            if (optionText != "" && $this.find("option[value='" + optionText + "']").length === 0) {
                var $select = $this.parents('.select2-container').prev("select");
                var newOption = new Option(optionText, optionText, true, true);
                $select.append(newOption).trigger('change');
                $this.val('');
                $select.select2('close');
            }
        }
    } catch (e) {
        console.error(e);
    }
});
JS
        );
    }

    public static function collectAssets()
    {
        Admin::collectComponentAssets('select2');
    }
}
