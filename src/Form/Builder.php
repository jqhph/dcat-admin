<?php

namespace Dcat\Admin\Form;

use Closure;
use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\UploadField;
use Dcat\Admin\Form;
use Dcat\Admin\Form\Field\Hidden;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\HasVariables;
use Dcat\Admin\Widgets\DialogForm;
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
    use HasVariables;

    /**
     *  上个页面URL保存的key.
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
     * @var Collection|Field[]
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
     * @var Field[]
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
    protected $view = 'admin::form.container';

    /**
     * Form title.
     *
     * @var string
     */
    protected $title;

    /**
     * @var Layout
     */
    protected $layout;

    /**
     * @var string
     */
    protected $elementId;

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
     * @var array
     */
    public $confirm = [];

    /**
     * Builder constructor.
     *
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->form = $form;
        $this->fields = new Collection();
        $this->layout = new Layout($form);
        $this->tools = new Tools($this);
        $this->footer = new Footer($this);
    }

    /**
     * @param \Closure $closure
     *
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
     *
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
     * Get form tools instance.
     *
     * @return Tools
     */
    public function tools()
    {
        return $this->tools;
    }

    /**
     * Get form footer instance.
     *
     * @return Footer
     */
    public function footer()
    {
        return $this->footer;
    }

    /**
     * @param string $title
     * @param string $content
     *
     * @return $this
     */
    public function confirm(?string $title = null, ?string $content = null)
    {
        $this->confirm['title'] = $title;
        $this->confirm['content'] = $content;

        return $this;
    }

    /**
     * Set the builder mode.
     *
     * @param string $mode
     *
     * @return void|string
     */
    public function mode(string $mode = null)
    {
        if ($mode === null) {
            return $this->mode;
        }

        $this->mode = $mode;
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
     * @return mixed|void
     */
    public function setResourceId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getResourceId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function resource($slice = null)
    {
        if ($this->mode == self::MODE_CREATE) {
            return $this->form->resource(-1);
        }
        if ($slice !== null) {
            return $this->form->resource($slice);
        }

        return $this->form->resource();
    }

    /**
     * @param int $field
     * @param int $label
     *
     * @return $this
     */
    public function width($field = 8, $label = 2)
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
     * Get or set action for form.
     *
     * @return string|void
     */
    public function action($action = null)
    {
        if ($action !== null) {
            $this->action = admin_url($action);

            return;
        }

        if ($this->action) {
            return $this->action;
        }

        if ($this->isMode(static::MODE_EDIT)) {
            return $this->form->resource().'/'.$this->id;
        }

        if ($this->isMode(static::MODE_CREATE)) {
            return $this->form->resource(-1);
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
    public function view($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get or set title for form.
     *
     * @param string $title
     *
     * @return $this|string
     */
    public function title($title = null)
    {
        if ($title !== null) {
            $this->title = $title;

            return $this;
        }

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
            if (is_array($field->column())) {
                $result = in_array($name, $field->column(), true) || $field->column() === $name ? $field : null;

                if ($result) {
                    return $result;
                }
            }

            return $field === $name || $field->column() === $name;
        });
    }

    /**
     * @param $column
     *
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
        return ! empty($this->form->rows());
    }

    /**
     * Get field rows of form.
     *
     * @return array
     */
    public function rows()
    {
        return $this->form->rows();
    }

    /**
     * @return Form
     */
    public function form()
    {
        return $this->form;
    }

    /**
     * @return array
     */
    public function hiddenFields()
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
     *
     * @return void
     */
    public function disableHeader(bool $disable = true)
    {
        $this->showHeader = ! $disable;
    }

    /**
     * @param bool $disable
     *
     * @return void
     */
    public function disableFooter(bool $disable = true)
    {
        $this->showFooter = ! $disable;
    }

    /**
     * @param $id
     *
     * @return void
     */
    public function setElementId($id)
    {
        $this->elementId = $id;
    }

    /**
     * @return string
     */
    public function getElementId()
    {
        return $this->elementId ?: ($this->elementId = 'form-'.Str::random(8));
    }

    public function pushField(Field $field)
    {
        $this->fields->push($field);

        return $this;
    }

    /**
     * Determine if form fields has files.
     *
     * @return bool
     */
    public function hasFile()
    {
        foreach ($this->fields() as $field) {
            if ($field instanceof UploadField) {
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
        $previous = Helper::getPreviousUrl();

        if (! $previous || $previous == URL::current()) {
            return;
        }

        if (
            Str::contains($previous, url($this->resource()))
            && ! Helper::urlHasQuery($previous, [DialogForm::QUERY_NAME])
        ) {
            $this->addHiddenField(
                (new Hidden(static::PREVIOUS_URL_KEY))->value($previous)
            );
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

        if ($this->isMode(static::MODE_EDIT)) {
            $this->addHiddenField((new Hidden('_method'))->value('PUT'));
        }

        $this->addRedirectUrlField();

        $attributes['id'] = $this->getElementId();
        $attributes['action'] = $this->action();
        $attributes['method'] = Arr::get($options, 'method', 'post');
        $attributes['accept-charset'] = 'UTF-8';
        $attributes['data-toggle'] = 'validator';
        $attributes['class'] = Arr::get($options, 'class');

        if ($this->hasFile()) {
            $attributes['enctype'] = 'multipart/form-data';
        }

        $html = [];
        foreach ($attributes as $name => $value) {
            $html[] = "$name=\"$value\"";
        }

        return '<form '.implode(' ', $html).' '.Admin::getPjaxContainerId().'>';
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
     * 移除需要忽略的字段.
     *
     * @return void
     */
    protected function removeIgnoreFields()
    {
        $this->fields = $this->fields()->reject(function (Field $field) {
            return $field->hasAttribute(Field::BUILD_IGNORE);
        });
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
            $this->form->keyName(),
            $this->form->createdAtColumn(),
            $this->form->updatedAtColumn(),
        ];

        $reject = function ($field) use (&$reservedColumns) {
            if ($field instanceof Field) {
                return in_array($field->column(), $reservedColumns, true)
                    && $field instanceof Form\Field\Display;
            }

            if ($field instanceof Row) {
                $fields = $field->fields()->reject(function ($item) use (&$reservedColumns) {
                    return in_array($item['element']->column(), $reservedColumns, true)
                        && $item['element'] instanceof Form\Field\Display;
                });

                $field->setFields($fields);
            }
        };

        $this->fields = $this->fields()->reject($reject);

        if ($this->form->hasTab()) {
            $this->form->getTab()->getTabs()->transform(function ($item) use ($reject) {
                if (! empty($item['fields'])) {
                    $item['fields'] = $item['fields']->reject($reject);
                }

                return $item;
            });
        }
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
        if (! $this->showFooter) {
            return;
        }

        return $this->footer->render();
    }

    protected function defaultVariables()
    {
        return [
            'form'       => $this,
            'tabObj'     => $this->form->getTab(),
            'width'      => $this->width,
            'elementId'  => $this->getElementId(),
            'showHeader' => $this->showHeader,
            'fields'     => $this->fields,
            'rows'       => $this->rows(),
            'layout'     => $this->layout(),
        ];
    }

    /**
     * Render form.
     *
     * @return string
     */
    public function render()
    {
        $this->removeIgnoreFields();
        $this->removeReservedFields();

        $tabObj = $this->form->getTab();

        if (! $tabObj->isEmpty()) {
            $tabObj->addScript();
        }

        if ($this->form->allowAjaxSubmit()) {
            $this->addSubmitScript();
        }

        $open = $this->open(['class' => 'form-horizontal']);

        if ($this->layout->hasColumns()) {
            $content = $this->doWrap(view($this->view, $this->variables()));
        } else {
            if (! $this->layout->hasBlocks()) {
                $this->layout->prepend(
                    12,
                    $this->doWrap(view($this->view, $this->variables()))
                );
            }

            $content = $this->layout->build(
                $this->renderHiddenFields()
            );
        }

        return "{$open}{$content}{$this->close()}";
    }

    /**
     * @param Renderable $view
     *
     * @return string
     */
    protected function doWrap(Renderable $view)
    {
        if ($wrapper = $this->wrapper) {
            return Helper::render($wrapper($view));
        }

        return "<div class='card'>{$view->render()}</div>";
    }

    /**
     * @return void
     */
    protected function addSubmitScript()
    {
        $confirm = admin_javascript_json($this->confirm);
        $toastr = $this->form->validationErrorToastr ? 'true' : 'false';

        Admin::script(
            <<<JS
$('#{$this->getElementId()}').form({
    validate: true,
    confirm: {$confirm},
    validationErrorToastr: $toastr,
});
JS
        );
    }

    public function renderHiddenFields()
    {
        $html = '';

        foreach ($this->hiddenFields as $field) {
            if (! $field->hasAttribute(Field::BUILD_IGNORE)) {
                $html .= $field->render();
            }
        }

        return $html;
    }
}
