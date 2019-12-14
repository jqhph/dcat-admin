<?php

namespace Dcat\Admin\Form;

use Closure;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Illuminate\Support\Arr;

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
        'selected' => 0,
        'width'    => '1000px',
        'padding'  => '30px 18px 30px',
        'remember' => false,
        'shown'    => [],
        'leaving'  => [],
    ];

    public function __construct(Form $form)
    {
        $this->form = $form->saved(function () {
            $this->flushStash();
        });

        $this->collectAssets();
    }

    /**
     * @param string|StepForm|StepForm[] $title
     * @param \Closure|null              $callback
     *
     * @return $this
     */
    public function add($title, ?\Closure $callback = null)
    {
        if (is_array($title)) {
            foreach ($title as $key => $form) {
                $this->addForm($form, $callback);
            }

            return $this;
        }

        $form = $title instanceof StepForm ? $title : new StepForm($title);

        $this->addForm($form, $callback);

        return $this;
    }

    /**
     * @param StepForm      $form
     * @param \Closure|null $callback
     *
     * @return void
     */
    protected function addForm(StepForm $form, ?\Closure $callback = null)
    {
        $form->setForm($this->form);
        $form->setIndex(count($this->stepForms));

        $this->stepForms[] = $form;

        if ($callback) {
            $callback($form);
        }
    }

    /**
     * Get all step forms.
     *
     * @return StepForm[]
     */
    public function all()
    {
        return $this->stepForms;
    }

    /**
     * Counts all step forms.
     *
     * @return int
     */
    public function count()
    {
        return count($this->stepForms);
    }

    /**
     * Set options.
     *
     * @param string|array $key
     * @param mixed        $value
     *
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
     * Get options.
     *
     * @param string|null $key
     * @param null        $default
     *
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
     * @param int $index
     *
     * @return $this
     */
    public function select(int $index)
    {
        return $this->option('selected', $index);
    }

    /**
     * Set padding for container.
     *
     * @param string $padding
     *
     * @return $this
     */
    public function padding(string $padding)
    {
        return $this->option('padding', $padding);
    }

    /**
     * Set max width for container.
     *
     * @param string $width
     *
     * @return $this
     */
    public function width(string $width)
    {
        return $this->option('width', $width);
    }

    /**
     * Remember input data.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function remember(bool $value = true)
    {
        return $this->option('remember', $value);
    }

    /**
     * @param string|Closure $title
     * @param Closure|null   $callback
     *
     * @return $this
     */
    public function done($title, Closure $callback = null)
    {
        if ($title instanceof Closure) {
            $callback = $title;
            $title = trans('admin.done');
        }

        $this->doneStep = new DoneStep($this->form, $title, $callback);

        return $this;
    }

    /**
     * @return DoneStep|null
     */
    public function doneStep()
    {
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
     * Stash input data.
     *
     * @param array $data
     * @param bool  $merge
     *
     * @return void
     */
    public function stash(array $data, bool $merge = false)
    {
        if (! $this->options['remember']) {
            return;
        }

        if ($merge) {
            $data = array_merge($this->fetchStash(), $data);
        }

        session()->put($this->stashKey(), $data);
    }

    /**
     * Fetch input data.
     *
     * @return array
     */
    public function fetchStash()
    {
        if (! $this->options['remember']) {
            return [];
        }

        return session()->get($this->stashKey()) ?: [];
    }

    /**
     * Flush input data.
     *
     * @return void
     */
    public function flushStash()
    {
        if (! $this->options['remember']) {
            return;
        }

        session()->remove($this->stashKey());
    }

    /**
     * Forget input data by keys.
     *
     * @param string|array $keys
     *
     * @return void
     */
    public function forgetStash($keys)
    {
        $data = $this->fetchStash();

        Arr::forget($data, $keys);

        $this->stash($data);
    }

    /**
     * @param string|Field $field
     *
     * @return void
     */
    public function stashIndexByField($field)
    {
        if (! $this->options['remember']) {
            return;
        }

        $data = $this->fetchStash();

        $data[self::CURRENT_VALIDATION_STEP] = ($this->fieldIndex($field) ?: 0) - 1;

        unset($data[self::ALL_STEPS]);

        $this->stash($data);
    }

    /**
     * @return string
     */
    protected function stashKey()
    {
        return 'step-form-input:'.admin_controller_slug();
    }

    /**
     * @return void
     */
    protected function collectAssets()
    {
        Admin::js('vendor/dcat-admin/SmartWizard/dist/js/jquery.smartWizard.min.js');
        Admin::css('vendor/dcat-admin/SmartWizard/dist/css/step.min.css');
    }

    /**
     * @return void
     */
    protected function selectStep()
    {
        if (! $this->options['remember'] || ! $input = $this->fetchStash()) {
            return;
        }

        $current = $input[static::CURRENT_VALIDATION_STEP] ?? null;
        $allStep = $input[static::ALL_STEPS] ?? null;

        unset($input[static::CURRENT_VALIDATION_STEP], $input[static::ALL_STEPS]);

        if ($current !== null && $current !== '' && ! empty($input)) {
            $this->select((int) ($current + 1));
        }

        if (! empty($allStep) && ! empty($input)) {
            $this->select($this->count() - 1);
        }
    }

    /**
     * @return string
     */
    public function build()
    {
        $this->selectStep();

        $this->setAction();

        return $this->renderFields();
    }

    /**
     * @return void
     */
    protected function setAction()
    {
        foreach ($this->stepForms as $step) {
            $step->action($this->form->action());

            foreach ($step->fields() as $field) {
                if ($field instanceof Form\Field\File) {
                    $field->setForm($this->form);
                }
            }
        }
    }

    /**
     * @return string
     */
    public function renderFields()
    {
        $html = '';

        foreach ($this->stepForms as $step) {
            $html .= (string) $step->render();
        }

        return $html;
    }

    /**
     * Register the "showStep" event listener.
     *
     * @param string $script
     *
     * @return $this
     */
    public function shown($script)
    {
        $script = value($script);

        $this->options['shown'][] = <<<JS
function (args) {
    {$script}
}
JS;

        return $this;
    }

    /**
     * Register the "leaveStep" event listener.
     *
     * @param string $script
     *
     * @return $this
     */
    public function leaving($script)
    {
        $script = value($script);

        $this->options['leaving'][] = <<<JS
function (args) {
    {$script}
}
JS;

        return $this;
    }

    /**
     * @param string|Field $column
     *
     * @return false|int
     */
    public function fieldIndex($column)
    {
        foreach ($this->stepForms as $index => $form) {
            if ($form->field($column)) {
                return $index;
            }
        }

        return false;
    }
}
