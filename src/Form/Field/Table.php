<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;
use Dcat\Admin\Form\NestedForm;
use Dcat\Admin\Support\Helper;

class Table extends HasMany
{
    /**
     * @var string
     */
    protected $viewMode = 'table';

    /**
     * Table constructor.
     *
     * @param string $column
     * @param array  $arguments
     */
    public function __construct($column, $arguments = [])
    {
        $this->column = $column;

        if (count($arguments) == 1) {
            $this->label = $this->formatLabel();
            $this->builder = $arguments[0];
        } elseif (count($arguments) == 2) {
            [$this->label, $this->builder] = $arguments;
        }
    }

    /**
     * @return array
     */
    protected function buildRelatedForms()
    {
        if (is_null($this->form)) {
            return [];
        }

        $forms = [];

        if ($values = old($this->column)) {
            foreach ($values as $key => $data) {
                if ($data[NestedForm::REMOVE_FLAG_NAME] == 1) {
                    continue;
                }

                $forms[$key] = $this->buildNestedForm($key)->fill($data);
            }
        } else {
            foreach ($this->value() as $key => $data) {
                if (isset($data['pivot'])) {
                    $data = array_merge($data, $data['pivot']);
                }

                $forms[$key] = $this->buildNestedForm($key)->fill($data);
            }
        }

        return $forms;
    }

    protected function prepareInputValue($input)
    {
        $form = $this->buildNestedForm();
        $prepare = $form->prepare($input);

        return array_values(
            collect($prepare)->reject(function ($item) {
                return ($item[NestedForm::REMOVE_FLAG_NAME] ?? null) == 1;
            })->map(function ($item) {
                unset($item[NestedForm::REMOVE_FLAG_NAME]);

                return $item;
            })->toArray()
        );
    }

    public function value($value = null)
    {
        if ($value === null) {
            return Helper::array(parent::value($value));
        }

        return parent::value($value);
    }

    public function buildNestedForm($key = null)
    {
        $form = new NestedForm($this->column);

        $form->setForm($this->form)
            ->setKey($key);

        call_user_func($this->builder, $form);

        $form->hidden(NestedForm::REMOVE_FLAG_NAME)->default(0)->addElementClass(NestedForm::REMOVE_FLAG_CLASS);

        return $form;
    }

    public function render()
    {
        if (! $this->shouldRender()) {
            return '';
        }

        Admin::style(
            <<<'CSS'
.table-has-many .fields-group .form-group {
    margin-bottom:0;
}
.table-has-many .fields-group .form-group .remove {
    margin-top: 10px;
}
CSS
        );

        return $this->renderTable();
    }
}
