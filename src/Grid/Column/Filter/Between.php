<?php

namespace Dcat\Admin\Grid\Column\Filter;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid\Column\Filter;
use Dcat\Admin\Grid\Model;

class Between extends Filter
{
    protected $dateFormat = null;

    /**
     * @var bool
     */
    protected $timestamp = false;

    public function __construct()
    {
        $this->class = [
            'start' => uniqid('column-filter-start-'),
            'end'   => uniqid('column-filter-end-'),
        ];
    }

    /**
     * Convert the datetime into unix timestamp.
     *
     * @return $this
     */
    public function toTimestamp()
    {
        $this->timestamp = true;

        return $this;
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
    public function datetime()
    {
        return $this->setDateFormat('YYYY-MM-DD HH:mm:ss');
    }

    /**
     * @param  string  $format
     * @return $this
     */
    protected function setDateFormat($format)
    {
        $this->dateFormat = $format;

        $this->requireAssets();

        return $this;
    }

    /**
     * Add a binding to the query.
     *
     * @param  mixed  $value
     * @param  Model  $model
     */
    public function addBinding($value, Model $model)
    {
        $value = array_filter((array) $value);

        if (empty($value)) {
            return;
        }

        if ($this->timestamp) {
            $value = array_map(function ($v) {
                if ($v) {
                    return strtotime($v);
                }
            }, $value);
        }

        if (! isset($value['start'])) {
            $this->withQuery($model, 'where', ['<=', $value['end']]);

            return;
        }

        if (! isset($value['end'])) {
            $this->withQuery($model, 'where', ['>=', $value['start']]);

            return;
        }

        $this->withQuery($model, 'whereBetween', [array_values($value)]);
    }

    protected function addScript()
    {
        if (! $this->dateFormat) {
            return;
        }

        $options = [
            'locale'           => config('app.locale'),
            'allowInputToggle' => true,
            'format'           => $this->dateFormat,
            'extraFormats' => ['DD-MM-YYYY', 'DD/MM/YYYY', 'DD.MM.YYYY', 'DD,MM,YYYY'],
        ];

        $options = json_encode($options);

        Admin::script(<<<JS
        $('.{$this->class['start']}').datetimepicker($options);
        $('.{$this->class['end']}').datetimepicker($options);
JS
        );
//        $('.{$this->class['start']}').on("dp.change", function (e) {
//            $('.{$this->class['end']}').data("DateTimePicker").minDate(e.date);
//        });
//        $('.{$this->class['end']}').on("dp.change", function (e) {
//            $('.{$this->class['start']}').data("DateTimePicker").maxDate(e.date);
//        });
    }

    /**
     * Render this filter.
     *
     * @return string
     */
    public function render()
    {
        if (! $this->shouldDisplay()) {
            return;
        }

        $script = <<<'JS'
$('.dropdown-menu input').on('click', function(e) {
    e.stopPropagation();
});
JS;

        Admin::script($script);

        $this->addScript();

        $value = $this->value(['start' => '', 'end' => '']);
        $active = empty(array_filter($value)) ? '' : 'active';

        return <<<EOT
&nbsp;<span class="dropdown">
<form action="{$this->formAction()}" pjax-container style="display: inline-block;">
    <a href="javascript:void(0);" class="{$active}" data-toggle="dropdown">
        <i class="feather icon-filter"></i>
    </a>
    <ul class="dropdown-menu" role="menu" style="min-width: 180px;padding: 10px;left: -70px;border-radius: 0;font-weight:normal;background:#fff">
        <li class="dropdown-item">
            <input type="text" 
                class="form-control input-sm {$this->class['start']}" 
                name="{$this->getQueryName()}[start]" 
                placeholder="{$this->trans('between_start')}" 
                value="{$value['start']}" 
                autocomplete="off" />
        </li>
        <li style="margin: 5px;"></li>
        <li class="dropdown-item">
            <input type="text" 
                class="form-control input-sm {$this->class['end']}" 
                name="{$this->getQueryName()}[end]"  
                placeholder="{$this->trans('between_end')}" 
                value="{$value['end']}" 
                autocomplete="off"/>
        </li>
        {$this->renderFormButtons()}
    </ul>
    </form>
</span>
EOT;
    }

    protected function requireAssets()
    {
        Admin::requireAssets(['moment', 'bootstrap-datetimepicker']);
    }
}
