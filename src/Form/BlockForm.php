<?php

namespace Dcat\Admin\Form;

use Dcat\Admin\Form;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\Form as WidgetForm;

class BlockForm extends WidgetForm
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var string
     */
    protected $title;

    public function __construct(Form $form)
    {
        $this->form = $form;
        $this->builder = $form->builder();

        $this->initFields();

        $this->initFormAttributes();
    }

    public function title($title)
    {
        $this->title = $title;

        return $this;
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
        $this->form->builder()->fields()->push($field);
        $this->fields->push($field);

        $field->attribute(Builder::BUILD_IGNORE, true);

        $field->setForm($this->form);
        $field->width($this->width['field'], $this->width['label']);

        $field::collectAssets();

        return $this;
    }

    public function render()
    {
        $view = Helper::render(parent::render());

        $style = $this->title ? '' : 'padding-top: 13px';

        return <<<HTML
<div class='box' style="{$style}">
    {$this->renderHeader()} 
    {$view}
</div>
HTML;
    }

    public function fillFields(array $data)
    {
    }

    protected function renderHeader()
    {
        if (! $this->title) {
            return;
        }

        return <<<HTML
<div class="box-header with-border mb-1">
    <h3 class="box-title">{$this->title}</h3>
</div>
HTML;
    }
}
