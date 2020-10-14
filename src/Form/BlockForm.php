<?php

namespace Dcat\Admin\Form;

use Dcat\Admin\Exception\RuntimeException;
use Dcat\Admin\Form;
use Dcat\Admin\Widgets\Form as WidgetForm;

/**
 * Class BlockForm
 *
 * @package Dcat\Admin\Form
 *
 * @mixin Form
 */
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

    /**
     * @var \Dcat\Admin\Layout\Row
     */
    public $layoutRow;

    public function __construct(Form $form)
    {
        $this->form = $form;
        $this->builder = $form->builder();

        $this->initFields();

        $this->initFormAttributes();
    }

    /**
     * 设置标题.
     *
     * @param string $title
     *
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * 显示底部内容.
     *
     * @return $this
     */
    public function showFooter()
    {
        $this->ajax(true);
        $this->disableSubmitButton(false);
        $this->disableResetButton(false);

        return $this;
    }

    /**
     * 在当前列增加一块表单.
     *
     * @param \Closure $callback
     *
     * @return $this
     */
    public function next(\Closure $callback)
    {
        $this->layoutRow->column(
            12,
            $form = $this->form
                ->builder()
                ->layout()
                ->form()
        );

        $callback($form);

        return $this;
    }

    public function pushField(Field $field)
    {
        $this->form->builder()->fields()->push($field);
        $this->fields->push($field);

        if ($this->layout()->hasColumns()) {
            $this->layout()->addField($field);
        }

        $field->attribute(Builder::BUILD_IGNORE, true);

        $field->setForm($this->form);
        $field->width($this->width['field'], $this->width['label']);

        $field::requireAssets();

        return $this;
    }

    public function render()
    {
        $class = $this->title ? '' : 'pt-1';

        $view = parent::render();

        return <<<HTML
<div class='box {$class} mb-1'>
    {$this->renderHeader()} {$view}
</div>
HTML;
    }

    protected function renderHeader()
    {
        if (! $this->title) {
            return;
        }

        return <<<HTML
<div class="box-header with-border" style="margin-bottom: .5rem">
    <h3 class="box-title">{$this->title}</h3>
</div>
HTML;
    }

    public function getKey()
    {
        return $this->form->getKey();
    }

    public function __call($method, $arguments)
    {
        try {
            return parent::__call($method, $arguments);
        } catch (RuntimeException $e) {
            return $this->form->$method($arguments);
        }
    }

    public function fillFields(array $data)
    {
    }

}
