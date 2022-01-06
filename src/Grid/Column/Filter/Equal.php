<?php

namespace Dcat\Admin\Grid\Column\Filter;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid\Column\Filter;
use Dcat\Admin\Grid\Model;

class Equal extends Filter
{
    use Input;

    /**
     * @var string
     */
    protected $dateFormat;

    /**
     * InputFilter constructor.
     *
     * @param  string  $type
     */
    public function __construct(?string $placeholder = null)
    {
        $this->placeholder($placeholder ?: $this->trans('search'));

        $this->class = uniqid('column-filter-');
    }

    /**
     * Date filter.
     *
     * @return $this
     */
    public function date()
    {
        return $this->setDateFormat('YYYY-MM-DD');
    }

    /**
     * Time filter.
     *
     * @return $this
     */
    public function time()
    {
        return $this->setDateFormat('HH:mm:ss');
    }

    /**
     * Datetime filter.
     *
     * @return $this
     */
    public function datetime(string $format = 'YYYY-MM-DD HH:mm:ss')
    {
        return $this->setDateFormat($format);
    }

    /**
     * @param  string  $format
     * @return $this
     */
    protected function setDateFormat($format)
    {
        $this->dateFormat = $format;

        $this->requireAssets();
        $this->addDateScript();

        return $this;
    }

    protected function addDateScript()
    {
        $options = [
            'locale'           => config('app.locale'),
            'allowInputToggle' => true,
            'format'           => $this->dateFormat,
        ];

        $options = admin_javascript_json($options);

        Admin::script("$('.{$this->class}').datetimepicker($options);");
    }

    /**
     * Add a binding to the query.
     *
     * @param  string  $value
     * @param  Model|null  $model
     */
    public function addBinding($value, Model $model)
    {
        $value = trim($value);
        if ($value === '') {
            return;
        }

        $this->withQuery($model, 'where', [$value]);
    }

    /**
     * Render this filter.
     *
     * @return string
     */
    public function render()
    {
        return $this->renderInput();
    }

    protected function requireAssets()
    {
        Admin::requireAssets(['moment', 'bootstrap-datetimepicker']);
    }
}
