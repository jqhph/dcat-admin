<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\Checkbox as WidgetCheckbox;

class Checkbox extends MultipleSelect
{
    use CanCascadeFields;

    public static $css = [];
    public static $js = [];

    protected $style = 'primary';

    protected $cascadeEvent = 'change';

    protected $canCheckAll = false;

    /**
     * @param array|\Closure|string $options
     *
     * @return $this|mixed
     */
    public function options($options = [])
    {
        if ($options instanceof \Closure) {
            $this->options = $options;

            return $this;
        }

        $this->options = Helper::array($options);

        return $this;
    }

    /**
     * "info", "primary", "inverse", "danger", "success", "purple".
     *
     * @param string $style
     *
     * @return $this
     */
    public function style(string $style)
    {
        $this->style = $style;

        return $this;
    }

    /**
     * Add a checkbox above this component, so you can select all checkboxes by click on it.
     *
     * @return $this
     */
    public function canCheckAll()
    {
        $this->canCheckAll = true;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        if ($this->options instanceof \Closure) {
            $this->options(
                $this->options->call($this->values(), $this->value(), $this)
            );
        }

        $this->addCascadeScript();

        $checkbox = WidgetCheckbox::make(
            $this->getElementName().'[]',
            $this->options,
            $this->style
        );

        if ($this->attributes['disabled'] ?? false) {
            $checkbox->disable();
        }

        $checkbox
            ->inline()
            ->check(old($this->column, $this->value()))
            ->class($this->getElementClassString());

        $this->addVariables([
            'checkbox' => $checkbox,
            'checkAll' => $this->makeCheckAllCheckbox(),
        ]);

        $this->script = ';';

        return parent::render();
    }

    protected function makeCheckAllCheckbox()
    {
        if (! $this->canCheckAll) {
            return;
        }

        $this->addCheckAllScript();

        return WidgetCheckbox::make('_check_all_', [__('admin.all')]);
    }

    protected function addCheckAllScript()
    {
        Admin::script(
            <<<'JS'
$('[name="_check_all_"]').on('change', function () {
    $(this).parents('.form-field').find('input[type="checkbox"]').prop('checked', this.checked);
});
JS
        );
    }
}
