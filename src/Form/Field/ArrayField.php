<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Form\NestedForm;
use Dcat\Admin\Support\Helper;

class ArrayField extends HasMany
{
    public function __construct($column, $arguments = [])
    {
        $this->column = $column;

        if (count($arguments) == 1) {
            $this->label = $this->formatLabel();
            $this->builder = $arguments[0];
        } elseif (count($arguments) == 2) {
            [$this->label, $this->builder] = $arguments;
        }

        $this->columnClass = $this->formatClass($column);
    }

    protected function buildRelatedForms()
    {
        if (is_null($this->form)) {
            return [];
        }

        $forms = [];

        foreach (Helper::array($this->value()) as $key => $data) {
            if (isset($data['pivot'])) {
                $data = array_merge($data, $data['pivot']);
            }

            $forms[$key] = $this->buildNestedForm($key)->fill($data);
        }

        return $forms;
    }

    protected function prepareInputValue($input)
    {
        return collect($this->buildNestedForm()->prepare($input))
            ->filter(function ($item) {
                return empty($item[NestedForm::REMOVE_FLAG_NAME]);
            })
            ->transform(function ($item) {
                unset($item[NestedForm::REMOVE_FLAG_NAME]);

                return $item;
            })
            ->values()
            ->toArray();
    }

    public function buildNestedForm($key = null)
    {
        $form = new NestedForm($this->getNestedFormColumnName());

        $this->setNestedFormDefaultKey($form);

        $form->setForm($this->form)
            ->setKey($key);

        $form->setResolvingFieldCallbacks($this->resolvingFieldCallbacks);

        call_user_func($this->builder, $form);

        $hidden = $form->hidden(NestedForm::REMOVE_FLAG_NAME)->default(0)->addElementClass(NestedForm::REMOVE_FLAG_CLASS);

        // 使用column布局之后需要重新追加字段
        $form->layout()->appendToLastColumn($hidden);

        return $form;
    }
}
