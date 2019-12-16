<?php

namespace Dcat\Admin\Form;

use Dcat\Admin\Widgets\Checkbox;
use Illuminate\Contracts\Support\Renderable;

class Footer implements Renderable
{
    /**
     * Footer view.
     *
     * @var string
     */
    protected $view = 'admin::form.footer';

    /**
     * Form builder instance.
     *
     * @var Builder
     */
    protected $builder;

    /**
     * Available buttons.
     *
     * @var array
     */
    protected $buttons = ['reset' => true, 'submit' => true];

    /**
     * Available checkboxes.
     *
     * @var array
     */
    protected $checkboxes = ['view' => true, 'continue_editing' => true, 'continue_creating' => true];

    /**
     * Footer constructor.
     *
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Disable reset button.
     *
     * @param bool $disable
     *
     * @return $this
     */
    public function disableReset(bool $disable = true)
    {
        $this->buttons['reset'] = ! $disable;

        return $this;
    }

    /**
     * Disable submit button.
     *
     * @param bool $disable
     *
     * @return $this
     */
    public function disableSubmit(bool $disable = true)
    {
        $this->buttons['submit'] = ! $disable;

        return $this;
    }

    /**
     * Disable View Checkbox.
     *
     * @param bool $disable
     *
     * @return $this
     */
    public function disableViewCheck(bool $disable = true)
    {
        $this->checkboxes['view'] = ! $disable;

        return $this;
    }

    /**
     * Disable Editing Checkbox.
     *
     * @param bool $disable
     *
     * @return $this
     */
    public function disableEditingCheck(bool $disable = true)
    {
        $this->checkboxes['continue_editing'] = ! $disable;

        return $this;
    }

    /**
     * Disable Creating Checkbox.
     *
     * @param bool $disable
     *
     * @return $this
     */
    public function disableCreatingCheck(bool $disable = true)
    {
        $this->checkboxes['continue_creating'] = ! $disable;

        return $this;
    }

    /**
     * Build checkboxes.
     *
     * @return Checkbox|null
     */
    protected function buildCheckboxes()
    {
        if ($this->builder->isEditing()) {
            $this->disableCreatingCheck();
        }

        $options = [];

        if ($this->checkboxes['continue_editing']) {
            $options[1] = sprintf('<span class="text-80 text-bold">%s</span>', trans('admin.continue_editing'));
        }

        if ($this->checkboxes['continue_creating']) {
            $options[2] = sprintf('<span class="text-80 text-bold">%s</span>', trans('admin.continue_creating'));
        }

        if ($this->checkboxes['view']) {
            $options[3] = sprintf('<span class="text-80 text-bold">%s</span>', trans('admin.view'));
        }

        if (! $options) {
            return;
        }

        return (new Checkbox('after-save', $options))->inline()->circle(true);
    }

    /**
     * Render footer.
     *
     * @return string
     */
    public function render()
    {
        $data = [
            'buttons'    => $this->buttons,
            'checkboxes' => $this->buildCheckboxes(),
            'width'      => $this->builder->getWidth(),
        ];

        return view($this->view, $data)->render();
    }
}
