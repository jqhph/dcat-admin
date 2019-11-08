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
        $this->form    = $form;
        $this->builder = $form->builder();

        $this->initFormAttributes();
    }

    /**
     * Add a form field to form.
     *
     * @param Field $field
     * @return $this
     */
    public function pushField(Field &$field)
    {
        array_push($this->fields, $field);

        $field->setForm($this->form);

        $field::collectAssets();

        return $this;
    }
}
