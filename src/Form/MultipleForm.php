<?php

namespace Dcat\Admin\Form;

use Dcat\Admin\Form;
use Dcat\Admin\Widgets\Form as WidgetForm;

class MultipleForm extends WidgetForm
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * @var Builder
     */
    protected $builder;

    public function __construct(Form $form)
    {
        $this->form = $form;
        $this->builder = $form->builder();

        $this->initFields();

        $this->initFormAttributes();
    }

    /**
     * Add a form field to form.
     *
     * @param Field $field
     *
     * @return $this
     */
    public function pushField(Field &$field)
    {
        $this->fields->push($field);

        $field->setForm($this->form);

        $field::collectAssets();

        return $this;
    }
}
