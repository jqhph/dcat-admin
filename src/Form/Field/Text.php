<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;
use Dcat\Admin\Form\Field;
use Illuminate\Support\Str;

class Text extends Field
{
    use PlainInput;
    use Sizeable;

    public function __construct($column, $arguments = [])
    {
        if (static::class === self::class) {
            $this->prepend('<i class="feather icon-edit-2"></i>');
        }

        parent::__construct($column, $arguments);
    }

    /**
     * Render this filed.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        $this->initPlainInput();
        $this->initSize();

        $this->defaultAttribute('type', 'text')
            ->defaultAttribute('name', $this->getElementName())
            ->defaultAttribute('value', $this->value())
            ->defaultAttribute('class', 'form-control '.$this->getElementClassString())
            ->defaultAttribute('placeholder', $this->placeholder());

        $this->addVariables([
            'prepend' => $this->prepend,
            'append'  => $this->append,
        ]);

        return parent::render();
    }

    /**
     * Set input type.
     *
     * @param  string  $type
     * @return $this
     */
    public function type(string $type)
    {
        return $this->attribute('type', $type);
    }

    /**
     * Set "data-match" attribute.
     *
     * @see http://1000hz.github.io/bootstrap-validator/
     *
     * @param  string|Field  $field
     * @param  string  $error
     * @return $this
     */
    public function same($field, ?string $error = null)
    {
        $field = $field instanceof Field ? $field : $this->form->field($field);
        $name = $field->column();

        if ($name.'_confirmation' === $this->column) {
            $field->rules('confirmed');
        } else {
            $this->rules('nullable|same:'.$name);
        }

        $attributes = [
            'data-match'       => $field->getElementClassSelector(),
            'data-match-error' => str_replace(
                [':attribute', ':other'],
                [$field->label(), $this->label()],
                $error ?: trans('admin.validation.match')
            ),
        ];

        return $this->attribute($attributes);
    }

    /**
     * @param  int  $length
     * @param  string|null  $error
     * @return $this
     */
    public function minLength(int $length, ?string $error = null)
    {
        $this->rules('nullable|min:'.$length);

        return $this->attribute([
            'data-minlength'       => $length,
            'data-minlength-error' => str_replace(
                [':attribute', ':min'],
                [$this->label, $length],
                $error ?: trans('admin.validation.minlength')
            ),
        ]);
    }

    /**
     * @param  int  $length
     * @param  string|null  $error
     * @return $this
     */
    public function maxLength(int $length, ?string $error = null)
    {
        Admin::script(
            <<<'JS'
Dcat.validator.extend('maxlength', function ($el) {
    return $el.val().length > $el.attr('data-maxlength');
});
JS
        );

        $this->rules('max:'.$length);

        return $this->attribute([
            'data-maxlength'       => $length,
            'data-maxlength-error' => str_replace(
                [':attribute', ':max'],
                [$this->label, $length],
                $error ?: trans('admin.validation.maxlength')
            ),
        ]);
    }

    /**
     * Add inputmask to an elements.
     *
     * @param  array  $options
     * @return $this
     */
    public function inputmask($options)
    {
        Admin::js('@jquery.inputmask');

        $options = admin_javascript_json($options);

        $this->script = "Dcat.init('{$this->getElementClassSelector()}', function (self) {
            self.inputmask($options);
        });";

        return $this;
    }

    /**
     * @param  array  $options
     * @return array
     */
    protected function formatOptions($options)
    {
        $original = [];
        $toReplace = [];

        foreach ($options as $key => &$value) {
            if (is_array($value)) {
                $subArray = $this->formatOptions($value);
                $value = $subArray['options'];
                $original = array_merge($original, $subArray['original']);
                $toReplace = array_merge($toReplace, $subArray['toReplace']);
            } elseif (preg_match('/function.*?/', $value)) {
                $original[] = $value;
                $value = "%{$key}%";
                $toReplace[] = "\"{$value}\"";
            }
        }

        return compact('original', 'toReplace', 'options');
    }

    /**
     * Add datalist element to Text input.
     *
     * @param  array  $entries
     * @return $this
     */
    public function datalist($entries = [])
    {
        $id = Str::random(8);

        $this->defaultAttribute('list', "list-{$id}");

        $datalist = "<datalist id=\"list-{$id}\">";
        foreach ($entries as $k => $v) {
            $value = is_string($k) ? "value=\"{$k}\"" : '';

            $datalist .= "<option {$value}>{$v}</option>";
        }
        $datalist .= '</datalist>';

        Admin::script("$('#list-{$id}').parent().hide()");

        return $this->append($datalist);
    }
}
