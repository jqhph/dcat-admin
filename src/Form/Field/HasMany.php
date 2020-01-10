<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Form\Field;
use Dcat\Admin\Form\NestedForm;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Class HasMany.
 */
class HasMany extends Field
{
    /**
     * Relation name.
     *
     * @var string
     */
    protected $relationName = '';

    /**
     * Relation key name.
     *
     * @var string
     */
    protected $relationKeyName = 'id';

    /**
     * Form builder.
     *
     * @var \Closure
     */
    protected $builder = null;

    /**
     * Form data.
     *
     * @var array
     */
    protected $value = [];

    /**
     * View Mode.
     *
     * Supports `default` and `tab` currently.
     *
     * @var string
     */
    protected $viewMode = 'default';

    /**
     * Available views for HasMany field.
     *
     * @var array
     */
    protected $views = [
        'default' => 'admin::form.hasmany',
        'tab'     => 'admin::form.hasmanytab',
        'table'   => 'admin::form.hasmanytable',
    ];

    /**
     * Options for template.
     *
     * @var array
     */
    protected $options = [
        'allowCreate' => true,
        'allowDelete' => true,
    ];

    /**
     * Create a new HasMany field instance.
     *
     * @param $relationName
     * @param array $arguments
     */
    public function __construct($relationName, $arguments = [])
    {
        $this->relationName = $relationName;

        $this->column = $relationName;

        if (count($arguments) == 1) {
            $this->label = $this->formatLabel();
            $this->builder = $arguments[0];
        }

        if (count($arguments) == 2) {
            [$this->label, $this->builder] = $arguments;
        }
    }

    /**
     * Get validator for this field.
     *
     * @param array $input
     *
     * @return bool|Validator
     */
    public function getValidator(array $input)
    {
        if (! array_key_exists($this->column, $input)) {
            return false;
        }

        $input = Arr::only($input, $this->column);

        $form = $this->buildNestedForm($this->column, $this->builder);

        $rules = $attributes = $messages = [];

        /* @var Field $field */
        foreach ($form->fields() as $field) {
            if (! $fieldRules = $field->getRules()) {
                continue;
            }

            $column = $field->column();

            if (is_array($column)) {
                foreach ($column as $key => $name) {
                    $rules[$name.$key] = $fieldRules;
                }

                $this->resetInputKey($input, $column);
            } else {
                $rules[$column] = $fieldRules;
            }

            $attributes = array_merge(
                $attributes,
                $this->formatValidationAttribute($input, $field->label(), $column)
            );

            $messages = array_merge(
                $messages,
                $this->formatValidationMessages($input, $field->getValidationMessages())
            );
        }

        Arr::forget($rules, NestedForm::REMOVE_FLAG_NAME);

        if (empty($rules)) {
            return false;
        }

        $newRules = [];
        $newInput = [];

        foreach ($rules as $column => $rule) {
            foreach (array_keys($input[$this->column]) as $key) {
                if ($input[$this->column][$key][NestedForm::REMOVE_FLAG_NAME]) {
                    continue;
                }

                $newRules["{$this->column}.$key.$column"] = $rule;
                if (isset($input[$this->column][$key][$column]) &&
                    is_array($input[$this->column][$key][$column])) {
                    foreach ($input[$this->column][$key][$column] as $vkey => $value) {
                        $newInput["{$this->column}.$key.{$column}$vkey"] = $value;
                    }
                }
            }
        }

        if (empty($newInput)) {
            $newInput = $input;
        }

        return Validator::make($newInput, $newRules, array_merge($this->getValidationMessages(), $messages), $attributes);
    }

    /**
     * Format validation messages.
     *
     * @param array $input
     * @param array $messages
     *
     * @return array
     */
    protected function formatValidationMessages(array $input, array $messages)
    {
        $result = [];
        foreach ($input[$this->column] as $key => &$value) {
            $newKey = $this->column.'.'.$key;

            foreach ($messages as $k => $message) {
                $result[$newKey.'.'.$k] = $message;
            }
        }

        return $result;
    }

    /**
     * Format validation attributes.
     *
     * @param array  $input
     * @param string $label
     * @param string $column
     *
     * @return array
     */
    protected function formatValidationAttribute($input, $label, $column)
    {
        $new = $attributes = [];

        if (is_array($column)) {
            foreach ($column as $index => $col) {
                $new[$col.$index] = $col;
            }
        }

        foreach (array_keys(Arr::dot($input)) as $key) {
            if (is_string($column)) {
                if (Str::endsWith($key, ".$column")) {
                    $attributes[$key] = $label;
                }
            } else {
                foreach ($new as $k => $val) {
                    if (Str::endsWith($key, ".$k")) {
                        $attributes[$key] = $label."[$val]";
                    }
                }
            }
        }

        return $attributes;
    }

    /**
     * Reset input key for validation.
     *
     * @param array $input
     * @param array $column $column is the column name array set
     *
     * @return void.
     */
    protected function resetInputKey(array &$input, array $column)
    {
        /**
         * flip the column name array set.
         *
         * for example, for the DateRange, the column like as below
         *
         * ["start" => "created_at", "end" => "updated_at"]
         *
         * to:
         *
         * [ "created_at" => "start", "updated_at" => "end" ]
         */
        $column = array_flip($column);

        /**
         * $this->column is the inputs array's node name, default is the relation name.
         *
         * So... $input[$this->column] is the data of this column's inputs data
         *
         * in the HasMany relation, has many data/field set, $set is field set in the below
         */
        foreach ($input[$this->column] as $index => $set) {

            /*
             * foreach the field set to find the corresponding $column
             */
            foreach ($set as $name => $value) {
                /*
                 * if doesn't have column name, continue to the next loop
                 */
                if (! array_key_exists($name, $column)) {
                    continue;
                }

                /**
                 * example:  $newKey = created_atstart.
                 *
                 * Σ( ° △ °|||)︴
                 *
                 * I don't know why a form need range input? Only can imagine is for range search....
                 */
                $newKey = $name.$column[$name];

                /*
                 * set new key
                 */
                Arr::set($input, "{$this->column}.$index.$newKey", $value);
                /*
                 * forget the old key and value
                 */
                Arr::forget($input, "{$this->column}.$index.$name");
            }
        }
    }

    /**
     * Prepare input data for insert or update.
     *
     * @param array $input
     *
     * @return array
     */
    protected function prepareToSave($input)
    {
        $form = $this->buildNestedForm($this->column, $this->builder);

        return array_values(
            collect($form->setOriginal($this->original, $this->getKeyName())->prepare($input))
                ->reject(function ($item) {
                    return $item[NestedForm::REMOVE_FLAG_NAME] == 1;
                })
                ->map(function ($item) {
                    unset($item[NestedForm::REMOVE_FLAG_NAME]);

                    return $item;
                })
                ->toArray()
        );
    }

    /**
     * Build a Nested form.
     *
     * @param string   $column
     * @param \Closure $builder
     * @param null     $key
     *
     * @return NestedForm
     */
    protected function buildNestedForm($column, \Closure $builder, $key = null)
    {
        $form = new Form\NestedForm($column, $key);

        $form->setForm($this->form);

        call_user_func($builder, $form);

        $form->hidden($this->getKeyName());

        $form->hidden(NestedForm::REMOVE_FLAG_NAME)->default(0)->addElementClass(NestedForm::REMOVE_FLAG_CLASS);

        return $form;
    }

    /**
     * Get the HasMany relation key name.
     *
     * @return string
     */
    protected function getKeyName()
    {
        if (is_null($this->form)) {
            return;
        }

        return $this->relationKeyName;
    }

    /**
     * @param string $relationKeyName
     */
    public function setRelationKeyName(?string $relationKeyName)
    {
        $this->relationKeyName = $relationKeyName;

        return $this;
    }

    /**
     * Set view mode.
     *
     * @param string $mode currently support `tab` mode.
     *
     * @return $this
     *
     * @author Edwin Hui
     */
    public function mode($mode)
    {
        $this->viewMode = $mode;

        return $this;
    }

    /**
     * Use tab mode to showing hasmany field.
     *
     * @return HasMany
     */
    public function useTab()
    {
        return $this->mode('tab');
    }

    /**
     * Use table mode to showing hasmany field.
     *
     * @return HasMany
     */
    public function useTable()
    {
        return $this->mode('table');
    }

    /**
     * Build Nested form for related data.
     *
     * @throws \Exception
     *
     * @return array
     */
    protected function buildRelatedForms()
    {
        if (is_null($this->form)) {
            return [];
        }

        $forms = [];

        /*
         * If redirect from `exception` or `validation error` page.
         *
         * Then get form data from session flash.
         *
         * Else get data from database.
         */
        if ($values = old($this->column)) {
            foreach ($values as $key => $data) {
                if ($data[NestedForm::REMOVE_FLAG_NAME] == 1) {
                    continue;
                }

                $forms[$key] = $this->buildNestedForm($this->column, $this->builder, $key)
                    ->fill($data);
            }
        } else {
            if (is_array($this->value)) {
                foreach ($this->value as $idx => $data) {
                    $key = Arr::get($data, $this->getKeyName(), $idx);

                    $forms[$key] = $this->buildNestedForm($this->column, $this->builder, $key)
                        ->fill($data);
                }
            }
        }

        return $forms;
    }

    /**
     * Setup script for this field in different view mode.
     *
     * @param string $script
     *
     * @return void
     */
    protected function setupScript($script)
    {
        $method = 'setupScriptFor'.ucfirst($this->viewMode).'View';

        call_user_func([$this, $method], $script);
    }

    /**
     * Setup default template script.
     *
     * @param string $templateScript
     *
     * @return void
     */
    protected function setupScriptForDefaultView($templateScript)
    {
        $removeClass = NestedForm::REMOVE_FLAG_CLASS;
        $defaultKey = NestedForm::DEFAULT_KEY_NAME;

        /**
         * When add a new sub form, replace all element key in new sub form.
         *
         * @example comments[new___key__][title]  => comments[new_{index}][title]
         *
         * {count} is increment number of current sub form count.
         */
        $script = <<<JS
(function () {
    var index = 0;
$('#has-many-{$this->column}').on('click', '.add', function () {

    var tpl = $('template.{$this->column}-tpl');

    index++;

    var template = tpl.html().replace(/{$defaultKey}/g, index);
    $('.has-many-{$this->column}-forms').append(template);
    {$templateScript}
});

$('#has-many-{$this->column}').on('click', '.remove', function () {
    $(this).closest('.has-many-{$this->column}-form').hide();
    $(this).closest('.has-many-{$this->column}-form').find('.$removeClass').val(1);
});
})();
JS;

        Admin::script($script);
    }

    /**
     * Setup tab template script.
     *
     * @param string $templateScript
     *
     * @return void
     */
    protected function setupScriptForTabView($templateScript)
    {
        $removeClass = NestedForm::REMOVE_FLAG_CLASS;
        $defaultKey = NestedForm::DEFAULT_KEY_NAME;

        $script = <<<JS
(function () {
    $('#has-many-{$this->column} > .nav').off('click', 'i.close-tab').on('click', 'i.close-tab', function(){
    var \$navTab = $(this).siblings('a');
    var \$pane = $(\$navTab.attr('href'));
    if( \$pane.hasClass('new') ){
        \$pane.remove();
    }else{
        \$pane.removeClass('active').find('.$removeClass').val(1);
    }
    if(\$navTab.closest('li').hasClass('active')){
        \$navTab.closest('li').remove();
        $('#has-many-{$this->column} > .nav > li:nth-child(1) > a').tab('show');
    }else{
        \$navTab.closest('li').remove();
    }
});

var index = 0;
$('#has-many-{$this->column} > .header').off('click', '.add').on('click', '.add', function(){
    index++;
    var navTabHtml = $('#has-many-{$this->column} > template.nav-tab-tpl').html().replace(/{$defaultKey}/g, index);
    var paneHtml = $('#has-many-{$this->column} > template.pane-tpl').html().replace(/{$defaultKey}/g, index);
    $('#has-many-{$this->column} > .nav').append(navTabHtml);
    $('#has-many-{$this->column} > .tab-content').append(paneHtml);
    $('#has-many-{$this->column} > .nav > li:last-child a').tab('show');
    {$templateScript}
});

if ($('.has-error').length) {
    $('.has-error').parent('.tab-pane').each(function () {
        var tabId = '#'+$(this).attr('id');
        $('li a[href="'+tabId+'"] i').removeClass('hide');
    });
    
    var first = $('.has-error:first').parent().attr('id');
    $('li a[href="#'+first+'"]').tab('show');
}
})();
JS;

        Admin::script($script);
    }

    /**
     * Setup default template script.
     *
     * @param string $templateScript
     *
     * @return void
     */
    protected function setupScriptForTableView($templateScript)
    {
        $removeClass = NestedForm::REMOVE_FLAG_CLASS;
        $defaultKey = NestedForm::DEFAULT_KEY_NAME;

        /**
         * When add a new sub form, replace all element key in new sub form.
         *
         * @example comments[new___key__][title]  => comments[new_{index}][title]
         *
         * {count} is increment number of current sub form count.
         */
        $script = <<<JS
(function () {
    var index = 0;
    $('#has-many-{$this->column}').on('click', '.add', function () {
        var tpl = $('template.{$this->column}-tpl');
    
        index++;
    
        var template = tpl.html().replace(/{$defaultKey}/g, index);
        $('.has-many-{$this->column}-forms').append(template);
        {$templateScript}
    });
    
    $('#has-many-{$this->column}').on('click', '.remove', function () {
        $(this).closest('.has-many-{$this->column}-form').hide();
        $(this).closest('.has-many-{$this->column}-form').find('.$removeClass').val(1);
    });
})();
JS;

        Admin::script($script);
    }

    /**
     * Disable create button.
     *
     * @return $this
     */
    public function disableCreate()
    {
        $this->options['allowCreate'] = false;

        return $this;
    }

    /**
     * Disable delete button.
     *
     * @return $this
     */
    public function disableDelete()
    {
        $this->options['allowDelete'] = false;

        return $this;
    }

    /**
     * Render the `HasMany` field.
     *
     * @throws \Exception
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        if (! $this->shouldRender()) {
            return '';
        }

        if ($this->viewMode == 'table') {
            return $this->renderTable();
        }

        // specify a view to render.
        $this->view = $this->views[$this->viewMode];

        [$template, $script] = $this->buildNestedForm($this->column, $this->builder)
            ->getTemplateHtmlAndScript();

        $this->setupScript($script);

        return parent::render()->with([
            'forms'        => $this->buildRelatedForms(),
            'template'     => $template,
            'relationName' => $this->relationName,
            'options'      => $this->options,
        ]);
    }

    /**
     * Render the `HasMany` field for table style.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    protected function renderTable()
    {
        $headers = [];
        $fields = [];
        $hidden = [];
        $scripts = [];

        /* @var Field $field */
        foreach ($this->buildNestedForm($this->column, $this->builder)->fields() as $field) {
            if (is_a($field, Hidden::class)) {
                $hidden[] = $field->render();
            } else {
                /* Hide label and set field width 100% */
                $field->labelClass(['hidden']);
                $field->width(12, 0);
                $fields[] = $field->render();
                $headers[] = $field->label();
            }

            /*
             * Get and remove the last script of Admin::$script stack.
             */
            if ($field->getScript()) {
                $scripts[] = array_pop(Admin::$script);
            }
        }

        /* Build row elements */
        $template = array_reduce($fields, function ($all, $field) {
            $all .= "<td>{$field}</td>";

            return $all;
        }, '');

        /* Build cell with hidden elements */
        $template .= '<td class="hidden">'.implode('', $hidden).'</td>';

        $this->setupScript(implode("\r\n", $scripts));

        // specify a view to render.
        $this->view = $this->views[$this->viewMode];

        return parent::render()->with([
            'headers'      => $headers,
            'forms'        => $this->buildRelatedForms(),
            'template'     => $template,
            'relationName' => $this->relationName,
            'options'      => $this->options,
        ]);
    }
}
