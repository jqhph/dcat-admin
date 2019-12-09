<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;
use Dcat\Admin\Form\Field;

class Text extends Field
{
    use PlainInput;

    /**
     * Render this filed.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        $this->initPlainInput();

        $this->prepend('<i class="ti-pencil"></i>')
            ->defaultAttribute('type', 'text')
            ->defaultAttribute('id', $this->id)
            ->defaultAttribute('name', $this->elementName())
            ->defaultAttribute('value', old($this->column, $this->value()))
            ->defaultAttribute('class', 'form-control '.$this->elementClassString())
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
     * @param string $type
     *
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
     * @param string|Field $field
     * @param string       $error
     *
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
            'data-match'       => '#'.$field->elementId(),
            'data-match-error' => str_replace([':attribute', ':other'], [$this->label, $name], $error ?: trans('admin.validation.match')),
        ];

        return $this->attribute($attributes);
    }

    /**
     * @param int         $length
     * @param string|null $error
     *
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
     * @param int         $length
     * @param string|null $error
     *
     * @return $this
     */
    public function maxLength(int $length, ?string $error = null)
    {
        Admin::script(
            <<<'JS'
LA.extendValidator('maxlength', function ($el) {
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
     * @param array $options
     *
     * @return $this
     */
    public function inputmask($options)
    {
        $options = $this->jsonEncodeOptions($options);

        $this->script = "$('{$this->elementClassSelector()}').inputmask($options);";

        return $this;
    }

    /**
     * Encode options to Json.
     *
     * @param array $options
     *
     * @return $json
     */
    protected function jsonEncodeOptions($options)
    {
        $data = $this->prepareOptions($options);

        $json = json_encode($data['options']);

        $json = str_replace($data['toReplace'], $data['original'], $json);

        return $json;
    }

    /**
     * Prepare options.
     *
     * @param array $options
     *
     * @return array
     */
    protected function prepareOptions($options)
    {
        $original = [];
        $toReplace = [];

        foreach ($options as $key => &$value) {
            if (is_array($value)) {
                $subArray = $this->prepareOptions($value);
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
     * @param array $entries
     *
     * @return $this
     */
    public function datalist($entries = [])
    {
        $this->defaultAttribute('list', "list-{$this->id}");

        $datalist = "<datalist id=\"list-{$this->id}\">";
        foreach ($entries as $k => $v) {
            $value = is_string($k) ? "value=\"{$k}\"" : '';

            $datalist .= "<option {$value}>{$v}</option>";
        }
        $datalist .= '</datalist>';

        Admin::script("$('#list-{$this->id}').parent().hide()");

        return $this->append($datalist);
    }
}
