<?php

namespace Dcat\Admin\Form;

use Closure;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;

class StepBuilder
{
    const CURRENT_VALIDATION_STEP = 'CURRENT_VALIDATION_STEP';
    const ALL_STEPS = 'ALL_STEPS';

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var StepForm[]
     */
    protected $stepForms = [];

    /**
     * @var DoneStep
     */
    protected $doneStep;

    /**
     * @var array
     */
    protected $options = [
        'width' => '1000px',
    ];

    public function __construct(Form $form)
    {
        $this->form = $form;

        $this->collectAssets();
    }

    /**
     * @param string $title
     * @param \Closure $callback
     * @return $this
     */
    public function add(string $title, \Closure $callback)
    {
        $form = new StepForm($this->form, count($this->stepForms), $title);

        $this->stepForms[] = $form;

        $callback($form);

        return $this;
    }

    /**
     * @return StepForm[]
     */
    public function all()
    {
        return $this->stepForms;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->stepForms);
    }

    /**
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function option($key, $value = null)
    {
        if (is_array($key)) {
            $this->options = array_merge($this->options, $key);
        } else {
            $this->options[$key] = $value;
        }

        return $this;
    }

    /**
     * @param string|null $key
     * @param null $default
     * @return array|mixed|null
     */
    public function getOption($key = null, $default = null)
    {
        if ($key === null) {
            return $this->options;
        }

        return $this->options[$key] ?? $default;
    }

    /**
     * @param string $width
     * @return $this
     */
    public function width(string $width)
    {
        return $this->option('width', $width);
    }

    /**
     * @param string|Closure $title
     * @param Closure|null $callback
     * @return $this
     */
    public function done($title, Closure $callback = null)
    {
        if ($title instanceof Closure) {
            $callback = $title;
            $title    = trans('admin.done');
        }

        $this->doneStep = new DoneStep($this->form, $title, $callback);

        return $this;
    }

    /**
     * @return DoneStep|null
     */
    public function getDoneStep()
    {
        if (! $this->stepForms) {
            return;
        }

        if (! $this->doneStep) {
            $this->setDefaultDonePage();
        }

        return $this->doneStep;
    }

    /**
     * @return void
     */
    protected function setDefaultDonePage()
    {
        $this->done(function () {
            $resource = $this->form->getResource(0);

            $data = [
                'title'       => trans('admin.save_succeeded'),
                'description' => '',
                'createUrl'   => $resource.'/create',
                'backUrl'     => $resource,
            ];

            return view('admin::form.done-step', $data);
        });
    }

    /**
     * @return void
     */
    protected function collectAssets()
    {
        Admin::js('vendor/dcat-admin/SmartWizard/dist/js/jquery.smartWizard.min.js');
        Admin::css('vendor/dcat-admin/SmartWizard/dist/css/step.min.css');
    }

}
