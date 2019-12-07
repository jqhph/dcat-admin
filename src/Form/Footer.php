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
    protected $buttons = ['reset', 'submit'];

    /**
     * Available checkboxes.
     *
     * @var array
     */
    protected $checkboxes = ['view', 'continue_editing', 'continue_creating'];

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
     * @return $this
     */
    public function disableReset(bool $disable = true)
    {
        if ($disable) {
            array_delete($this->buttons, 'reset');
        } elseif (!in_array('reset', $this->buttons)) {
            array_push($this->buttons, 'reset');
        }

        return $this;
    }

    /**
     * Disable submit button.
     *
     * @return $this
     */
    public function disableSubmit(bool $disable = true)
    {
        if ($disable) {
            array_delete($this->buttons, 'submit');
        } elseif (!in_array('submit', $this->buttons)) {
            array_push($this->buttons, 'submit');
        }

        return $this;
    }

    /**
     * Disable View Checkbox.
     *
     * @return $this
     */
    public function disableViewCheck(bool $disable = true)
    {
        if ($disable) {
            array_delete($this->checkboxes, 'view');
        } elseif (!in_array('view', $this->checkboxes)) {
            array_push($this->checkboxes, 'view');
        }

        return $this;
    }

    /**
     * Disable Editing Checkbox.
     *
     * @return $this
     */
    public function disableEditingCheck(bool $disable = true)
    {
        if ($disable) {
            array_delete($this->checkboxes, 'continue_editing');
        } elseif (!in_array('continue_editing', $this->checkboxes)) {
            array_push($this->checkboxes, 'continue_editing');
        }

        return $this;
    }

    /**
     * Disable Creating Checkbox.
     *
     * @return $this
     */
    public function disableCreatingCheck(bool $disable = true)
    {
        if ($disable) {
            array_delete($this->checkboxes, 'continue_creating');
        } elseif (!in_array('continue_creating', $this->checkboxes)) {
            array_push($this->checkboxes, 'continue_creating');
        }

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

        if (in_array('continue_editing', $this->checkboxes)) {
            $options[1] = sprintf('<span class="text-80 text-bold">%s</span>', trans('admin.continue_editing'));
        }

        if (in_array('continue_creating', $this->checkboxes)) {
            $options[2] = sprintf('<span class="text-80 text-bold">%s</span>', trans('admin.continue_creating'));
        }

        if (in_array('view', $this->checkboxes)) {
            $options[3] =  sprintf('<span class="text-80 text-bold">%s</span>', trans('admin.view'));
        }

        if (!$options) {
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
