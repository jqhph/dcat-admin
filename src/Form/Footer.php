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
     * Footer view data.
     *
     * @var array
     */
    protected $data = [];

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
     * Default checked.
     *
     * @var arrays
     */
    protected $defaultcheckeds = ['view' => false, 'continue_editing' => false, 'continue_creating' => false];

    /**
     * Footer constructor.
     *
     * @param  Builder  $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Disable reset button.
     *
     * @param  bool  $disable
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
     * @param  bool  $disable
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
     * @param  bool  $disable
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
     * @param  bool  $disable
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
     * @param  bool  $disable
     * @return $this
     */
    public function disableCreatingCheck(bool $disable = true)
    {
        $this->checkboxes['continue_creating'] = ! $disable;

        return $this;
    }

    /**
     * default View Checked.
     *
     * @param  bool  $checked
     * @return $this
     */
    public function defaultViewChecked(bool $checked = true)
    {
        $this->defaultcheckeds['view'] = $checked;

        return $this;
    }

    /**
     * default Editing Checked.
     *
     * @param  bool  $checked
     * @return $this
     */
    public function defaultEditingChecked(bool $checked = true)
    {
        $this->defaultcheckeds['continue_editing'] = $checked;

        return $this;
    }

    /**
     * default Creating Checked.
     *
     * @param  bool  $checked
     * @return $this
     */
    public function defaultCreatingChecked(bool $checked = true)
    {
        $this->defaultcheckeds['continue_creating'] = $checked;

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
        $checked = [];

        if ($this->checkboxes['continue_editing']) {
            $options[1] = sprintf('<span class="text-80 text-bold">%s</span>', trans('admin.continue_editing'));
        }

        if ($this->checkboxes['continue_creating']) {
            $options[2] = sprintf('<span class="text-80 text-bold">%s</span>', trans('admin.continue_creating'));
        }

        if ($this->checkboxes['view']) {
            $options[3] = sprintf('<span class="text-80 text-bold">%s</span>', trans('admin.view'));
        }

        if ($this->defaultcheckeds['continue_editing']) {
            $checked[] = 1;
        }

        if ($this->defaultcheckeds['continue_creating']) {
            $checked[] = 2;
        }

        if ($this->defaultcheckeds['view']) {
            $checked[] = 3;
        }

        if (! $options) {
            return;
        }

        return (new Checkbox('after-save', $options))->check($checked)->inline()->circle(true);
    }

    /**
     * Use custom view.
     *
     * @param  string  $view
     * @param  array  $data
     */
    public function view(string $view, array $data = [])
    {
        $this->view = $view;

        $this->data = $data;
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

        $data = array_merge($data, $this->data);

        return view($this->view, $data)->render();
    }
}
