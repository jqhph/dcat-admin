<?php

namespace Dcat\Admin\Form;

use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Widgets\Form as WidgetForm;

class StepForm extends WidgetForm
{
    const CURRENT_VALIDATION_STEP = 'CURRENT_VALIDATION_STEP';
    const ALL_STEPS = 'ALL_STEPS';

    /**
     * @var string
     */
    protected $view = 'admin::form.step-form';

    /**
     * @var array
     */
    protected $buttons = [];

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var int
     */
    protected $index;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * StepForm constructor.
     *
     * @param Form $form
     * @param int $index
     * @param string $title
     */
    public function __construct(Form $form, int $index = 0, string $title = null)
    {
        $this->form  = $form;
        $this->index = $index;

        $this->title($title);
    }

    /**
     * @param string|\Closure $title
     * @return $this
     */
    public function title($title)
    {
        $this->title = value($title);

        return $this;
    }

    /**
     * @param string|\Closure $content
     * @return $this
     */
    public function description($content)
    {
        $this->description = value($content);

        return $this;
    }

    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    protected function open()
    {
        $this->collectAssets();

        if ($this->index > 0) {
            $this->setHtmlAttribute('style', 'display:none');
        }

        $this->setHtmlAttribute('data-toggle', 'validator');
        $this->setHtmlAttribute('role', 'form');

        return <<<HTML
<div {$this->formatHtmlAttributes()}>
HTML;
    }

    /**
     * @return string
     */
    protected function close()
    {
        return '</div>';
    }

    protected function collectAssets()
    {
        Admin::js('vendor/dcat-admin/SmartWizard/dist/js/jquery.smartWizard.min.js');
        Admin::css('vendor/dcat-admin/SmartWizard/dist/css/step.min.css');
    }

}
