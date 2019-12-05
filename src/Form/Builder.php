<?php

namespace Dcat\Admin\Form;

use Closure;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Form\Field\Hidden;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * Class Builder.
 */
class Builder
{
    /**
     *  Previous url key.
     */
    const PREVIOUS_URL_KEY = '_previous_';

    /**
     * Modes constants.
     */
    const MODE_EDIT = 'edit';
    const MODE_CREATE = 'create';
    const MODE_DELETE = 'delete';

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var
     */
    protected $action;

    /**
     * @var Collection
     */
    protected $fields;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * Form action mode, could be create|view|edit.
     *
     * @var string
     */
    protected $mode = self::MODE_CREATE;

    /**
     * @var array
     */
    protected $hiddenFields = [];

    /**
     * @var Tools
     */
    protected $tools;

    /**
     * @var Footer
     */
    protected $footer;

    /**
     * Width for label and field.
     *
     * @var array
     */
    protected $width = [
        'label' => 2,
        'field' => 8,
    ];

    /**
     * View for this form.
     *
     * @var string
     */
    protected $view = 'admin::form';

    /**
     * Form title.
     *
     * @var string
     */
    protected $title;

    /**
     * @var MultipleForm[]
     */
    protected $multipleForms = [];

    /**
     * @var Layout
     */
    protected $layout;

    /**
     * @var int
     */
    protected $defaultBlockWidth = 12;

    /**
     * @var string
     */
    protected $formId;

    /**
     * @var \Closure
     */
    protected $wrapper;

    /**
     * @var bool
     */
    protected $showHeader = true;

    /**
     * @var bool
     */
    protected $showFooter = true;

    /**
     * @var StepBuilder
     */
    protected $stepBuilder;

    /**
     * Builder constructor.
     *
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->form   = $form;
        $this->fields = new Collection();
        $this->layout = new Layout($form);
        $this->tools  = new Tools($this);
        $this->footer = new Footer($this);
    }


    /**
     * @param \Closure $closure
     * @return Layout
     */
    public function layout($closure = null)
    {
        if ($closure) {
            $closure($this->layout);
        }

        return $this->layout;
    }

    /**
     * @param Closure $closure
     * @return $this;
     */
    public function wrap(Closure $closure)
    {
        $this->wrapper = $closure;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasWrapper()
    {
        return $this->wrapper ? true : false;
    }

    /**
     * @param int $width
     * @return $this
     */
    public function setDefaultBlockWidth(int $width)
    {
        $this->defaultBlockWidth = $width;

        return $this;
    }

    /**
     * @param MultipleForm $form
     */
    public function addForm(MultipleForm $form)
    {
        $this->multipleForms[] = $form;

        $form->disableResetButton();
        $form->disableSubmitButton();
        $form->disableFormTag();

        return $this;
    }

    /**
     * Get form tools instance.
     *
     * @return Tools
     */
    public function getTools()
    {
        return $this->tools;
    }

    /**
     * Get form footer instance.
     *
     * @return Footer
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * @param \Closure $callback
     * @return StepBuilder
     */
    public function step(?\Closure $callback = null)
    {
        if (! $this->stepBuilder) {
            $this->view = 'admin::form.steps';

            $this->stepBuilder = new StepBuilder($this->form);
        }

        if ($callback) {
            $callback($this->stepBuilder);
        }

        return $this->stepBuilder;
    }

    /**
     * @return StepBuilder
     */
    public function getStepBuilder()
    {
        return $this->stepBuilder;
    }

    /**
     * Set the builder mode.
     *
     * @param string $mode
     *
     * @return void
     */
    public function setMode(string $mode = self::MODE_CREATE)
    {
        $this->mode = $mode;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Returns builder is $mode.
     *
     * @param $mode
     *
     * @return bool
     */
    public function isMode($mode)
    {
        return $this->mode == $mode;
    }

    /**
     * Check if is creating resource.
     *
     * @return bool
     */
    public function isCreating()
    {
        return $this->isMode(static::MODE_CREATE);
    }

    /**
     * Check if is editing resource.
     *
     * @return bool
     */
    public function isEditing()
    {
        return $this->isMode(static::MODE_EDIT);
    }

    /**
     * Check if is deleting resource.
     *
     * @return bool
     */
    public function isDeleting()
    {
        return $this->isMode(static::MODE_DELETE);
    }

    /**
     * Set resource Id.
     *
     * @param $id
     *
     * @return void
     */
    public function setResourceId($id)
    {
        $this->id = $id;
    }

    /**
     * Get Resource id.
     *
     * @return mixed
     */
    public function getResourceId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getResource($slice = null)
    {
        if ($this->mode == self::MODE_CREATE) {
            return $this->form->getResource(-1);
        }
        if ($slice !== null) {
            return $this->form->getResource($slice);
        }

        return $this->form->getResource();
    }

    /**
     * @param int $field
     * @param int $label
     *
     * @return $this
     */
    public function setWidth($field = 8, $label = 2)
    {
        $this->width = [
            'label' => $label,
            'field' => $field,
        ];

        return $this;
    }

    /**
     * Get label and field width.
     *
     * @return array
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set form action.
     *
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Get Form action.
     *
     * @return string
     */
    public function getAction()
    {
        if ($this->action) {
            return $this->action;
        }

        if ($this->isMode(static::MODE_EDIT)) {
            return $this->form->getResource().'/'.$this->id;
        }

        if ($this->isMode(static::MODE_CREATE)) {
            return $this->form->getResource(-1);
        }

        return '';
    }

    /**
     * Set view for this form.
     *
     * @param string $view
     *
     * @return $this
     */
    public function setView($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Set title for form.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get fields of this builder.
     *
     * @return Collection
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * Get specify field.
     *
     * @param string|Field $name
     *
     * @return Field|null
     */
    public function field($name)
    {
        return $this->fields->first(function (Field $field) use ($name) {
            return $field === $name || $field->column() == $name;
        });
    }

    /**
     * @param string $name
     * @return Field|null
     */
    public function stepField($name)
    {
        if (! $builder = $this->getStepBuilder()) {
            return;
        }

        foreach ($builder->all() as $step) {
            if ($field = $step->field($name)) {
                return $field;
            }
        }
    }

    /**
     * @return Field[]|Collection
     */
    public function stepFields()
    {
        $fields = new Collection();

        if (! $builder = $this->getStepBuilder()) {
            return $fields;
        }

        foreach ($builder->all() as $step) {
            $fields = $fields->merge($step->fields());
        }

        return $fields;
    }

    /**
     * @param $column
     * @return void
     */
    public function removeField($column)
    {
        $this->fields = $this->fields->filter(function (Field $field) use ($column) {
            return $field->column() != $column;
        });
    }

    /**
     * If the parant form has rows.
     *
     * @return bool
     */
    public function hasRows()
    {
        return !empty($this->form->getRows());
    }

    /**
     * Get field rows of form.
     *
     * @return array
     */
    public function getRows()
    {
        return $this->form->getRows();
    }

    /**
     * @return array
     */
    public function getHiddenFields()
    {
        return $this->hiddenFields;
    }

    /**
     * @param Field $field
     *
     * @return void
     */
    public function addHiddenField(Field $field)
    {
        $this->hiddenFields[] = $field;
    }

    /**
     * Add or get options.
     *
     * @param array $options
     *
     * @return array|null
     */
    public function options($options = [])
    {
        if (empty($options)) {
            return $this->options;
        }

        $this->options = array_merge($this->options, $options);
    }

    /**
     * Get or set option.
     *
     * @param string $option
     * @param mixed  $value
     *
     * @return void
     */
    public function option($option, $value = null)
    {
        if (func_num_args() == 1) {
            return Arr::get($this->options, $option);
        }

        $this->options[$option] = $value;
    }

    /**
     * @param bool $disable
     * @return void
     */
    public function disableHeader(bool $disable = true)
    {
        $this->showHeader = !$disable;
    }

    /**
     * @param bool $disable
     * @return void
     */
    public function disableFooter(bool $disable = true)
    {
        $this->showFooter = !$disable;
    }

    /**
     * @param $id
     * @return void
     */
    public function setFormId($id)
    {
        $this->formId = $id;
    }

    /**
     * @return string
     */
    public function getFormId()
    {
        return $this->formId ?: ($this->formId = 'form-'.Str::random(8));
    }

    /**
     * @return string
     */
    public function title()
    {
        if ($this->title) {
            return $this->title;
        }

        if ($this->mode == static::MODE_CREATE) {
            return trans('admin.create');
        }

        if ($this->mode == static::MODE_EDIT) {
            return trans('admin.edit');
        }

        return '';
    }

    /**
     * Determine if form fields has files.
     *
     * @return bool
     */
    public function hasFile()
    {
        foreach ($this->fields() as $field) {
            if (
                $field instanceof Field\File
                || $field instanceof Form\Field\BootstrapFile
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add field for store redirect url after update or store.
     *
     * @return void
     */
    protected function addRedirectUrlField()
    {
        $previous = URL::previous();

        if (!$previous || $previous == URL::current()) {
            return;
        }

        if (Str::contains($previous, url($this->getResource()))) {
            $this->addHiddenField((new Hidden(static::PREVIOUS_URL_KEY))->value($previous));
        }
    }

    /**
     * Open up a new HTML form.
     *
     * @param array $options
     *
     * @return string
     */
    public function open($options = [])
    {
        $attributes = [];

        if ($this->isMode(self::MODE_EDIT)) {
            $this->addHiddenField((new Hidden('_method'))->value('PUT'));
        }

        $this->addRedirectUrlField();

        $attributes['id']             = $this->getFormId();
        $attributes['action']         = $this->getAction();
        $attributes['method']         = Arr::get($options, 'method', 'post');
        $attributes['accept-charset'] = 'UTF-8';
        $attributes['data-toggle']    = 'validator';
        $attributes['class']          = Arr::get($options, 'class');

        if ($this->hasFile()) {
            $attributes['enctype'] = 'multipart/form-data';
        }

        $html = [];
        foreach ($attributes as $name => $value) {
            $html[] = "$name=\"$value\"";
        }

        return '<form '.implode(' ', $html).' pjax-container>';
    }

    /**
     * Close the current form.
     *
     * @return string
     */
    public function close()
    {
        $this->form = null;
        $this->fields = null;

        return '</form>';
    }

    /**
     * Remove reserved fields like `id` `created_at` `updated_at` in form fields.
     *
     * @return void
     */
    protected function removeReservedFields()
    {
        if (! $this->isMode(static::MODE_CREATE)) {
            return;
        }

        $reservedColumns = [
            $this->form->getKeyName(),
            $this->form->getCreatedAtColumn(),
            $this->form->getUpdatedAtColumn(),
        ];

        $this->fields = $this->fields()->reject(function (Field $field) use (&$reservedColumns) {
            return in_array($field->column(), $reservedColumns)
                && $field instanceof Form\Field\Display;
        });
    }

    /**
     * Render form header tools.
     *
     * @return string
     */
    public function renderTools()
    {
        return $this->tools->render();
    }

    /**
     * Render form footer.
     *
     * @return string
     */
    public function renderFooter()
    {
        if (!$this->showFooter) {
            return;
        }

        return $this->footer->render();
    }

    /**
     * Render form.
     *
     * @return string
     */
    public function render()
    {
        $this->removeReservedFields();

        $tabObj = $this->form->getTab();

        if (!$tabObj->isEmpty()) {
            $this->setupTabScript();
        }

        if ($this->form->allowAjaxSubmit() && empty($this->stepBuilder)) {
            $this->setupSubmitScript();
        }

        $open = $this->open(['class' => 'form-horizontal']);

        $data = [
            'form'       => $this,
            'tabObj'     => $tabObj,
            'width'      => $this->width,
            'formId'     => $this->getFormId(),
            'showHeader' => $this->showHeader,
            'steps'      => $this->stepBuilder,
        ];

        $this->layout->prepend(
            $this->defaultBlockWidth,
            $this->doWrap(view($this->view, $data))
        );

        return <<<EOF
{$open} {$this->layout->build()} {$this->close()}
EOF;

    }

    /**
     * @param Renderable $view
     * @return string
     */
    protected function doWrap(Renderable $view)
    {
        if ($wrapper = $this->wrapper) {
            return $wrapper($view);
        }

        return "<div class='card material'>{$view->render()}</div>";
    }

    /**
     * @return void
     */
    protected function setupSubmitScript()
    {
        Admin::script(
            <<<JS
(function () {
    var f = $('#{$this->getFormId()}');

    f.find('[type="submit"]').click(function () {
        var t = $(this);
    
        LA.Form({
            \$form: f,
            before: function () {
                f.validator('validate');
        
                if (f.find('.has-error').length > 0) {
                    return false;
                }
                t.button('loading').removeClass('waves-effect');
            },
            after: function () {
                t.button('reset');
            }
        });
    
        return false;
    });
})()
JS

        );
    }

    /**
     * @return void
     */
    protected function setupTabScript()
    {
        $formId = $this->getFormId();

        $script = <<<JS
(function () {
    var hash = document.location.hash;
    if (hash) {
        $('#$formId .nav-tabs a[href="' + hash + '"]').tab('show');
    }
    
    // Change hash for page-reload
    $('#$formId .nav-tabs a').on('shown.bs.tab', function (e) {
        history.pushState(null,null, e.target.hash);
    });
    
    if ($('#$formId .has-error').length) {
        $('#$formId .has-error').each(function () {
            var tabId = '#'+$(this).closest('.tab-pane').attr('id');
            $('li a[href="'+tabId+'"] i').removeClass('hide');
        });
    
        var first = $('#$formId .has-error:first').closest('.tab-pane').attr('id');
        $('li a[href="#'+first+'"]').tab('show');
    }
})();
JS;
        Admin::script($script);
    }
}
