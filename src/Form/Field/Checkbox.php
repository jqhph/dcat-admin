<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\Checkbox as WidgetCheckbox;

class Checkbox extends MultipleSelect
{
    use CanCascadeFields;
    use CanLoadFields;
    use Sizeable;

    protected $style = 'primary';

    protected $cascadeEvent = 'change';

    protected $canCheckAll = false;

    protected $inline = true;

    /**
     * @param  array|\Closure|string  $options
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
     * @param  string  $style
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

    public function inline(bool $inline)
    {
        $this->inline = $inline;

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
            ->inline($this->inline)
            ->check($this->value())
            ->class($this->getElementClassString())
            ->size($this->size);

        $this->addVariables([
            'checkbox' => $checkbox,
            'checkAll' => $this->makeCheckAllCheckbox(),
        ]);

        return parent::render();
    }

    protected function makeCheckAllCheckbox()
    {
        if (! $this->canCheckAll) {
            return;
        }

        $this->addVariables(['canCheckAll' => $this->canCheckAll]);

        return WidgetCheckbox::make('_check_all_', [__('admin.all')]);
    }
}
