<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;
use Dcat\Admin\Form\Field;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\Color;
use Illuminate\Contracts\Support\Arrayable;

class SelectResource extends Field
{
    use PlainInput;

    protected static $js = [
        'vendor/dcat-admin/dcat-admin/select-resource.min.js',
    ];

    protected $area = ['51%', '65%'];

    protected $source;

    protected $maxItem = 1;

    protected $style = 'primary';

    protected $btnId;

    /**
     * Set window's area.
     *
     * @param string $width
     * @param string $height
     *
     * @return $this
     */
    public function area(string $width, string $height)
    {
        $this->area = [$width, $height];

        return $this;
    }

    /**
     * Set button style.
     *
     * @param string $style
     *
     * @return $this
     */
    public function style(string $style = 'primary')
    {
        $this->style = $style;

        return $this;
    }

    /**
     * Set the field options.
     *
     * @param array|\Closure $options
     *
     * @return $this
     */
    public function options($options = [])
    {
        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        $this->options = $options;

        return $this;
    }

    protected function formatOptions()
    {
        if ($this->options instanceof \Closure) {
            $value = Helper::array(old($this->column, $this->value()));

            $this->options = $this->options->call($this->values(), $value, $this);
        }

        $this->options = Helper::array($this->options);
    }

    /**
     * Multiple select.
     *
     * @param int|null|null $max
     *
     * @return SelectResource
     */
    public function multiple(?int $max = null)
    {
        return $this->max($max);
    }

    /**
     * @param ?int $max
     *
     * @return $this
     */
    public function max(?int $max)
    {
        $this->maxItem = $max;

        return $this;
    }

    /**
     * Set source path.
     *
     * @param string $source
     *
     * @return $this
     */
    public function path($source)
    {
        $this->source = admin_url($source);

        return $this;
    }

    protected function formatValue()
    {
        $value = Helper::array(old($this->column, $this->value));

        $this->value = [];

        foreach ($this->options as $id => $label) {
            foreach ($value as $v) {
                if ($v == $id && $v !== null) {
                    $this->value[$v] = $label;
                }
            }
        }

        $this->value = json_encode((object) $this->value);
    }

    protected function setDefaultSource()
    {
        if (! $this->source) {
            if (strpos($this->column, '.')) {
                $this->path(str_replace('_id', '', last(explode('.', $this->column))));
            } else {
                $this->path(str_replace('_id', '', $this->column));
            }
        }
    }

    protected function prepareToSave($value)
    {
        if ($this->maxItem == 1) {
            if ($value === null || $value === '') {
                return 0;
            }

            return $value;
        }

        return Helper::array($value, true);
    }

    protected function setupScript()
    {
        $label = ucfirst(trans('admin.choose')).' '.$this->label;
        $area = json_encode($this->area);
        $closeLabel = ucfirst(trans('admin.close'));
        $lessThenLabel = trans('admin.selected_must_less_then', ['num' => $this->maxItem]);
        $selectedOptionsLabel = trans('admin.selected_options');
        $disabled = empty($this->attributes['disabled']) ? '' : 'disabled';
        $containerId = $this->id.$this->formElementId();
        $maxItem = (int) $this->maxItem;

        Admin::script(
            <<<JS
LA.ResourceSelector({
    title: '{$label}',
    column: "{$this->elementName()}",
    source: '{$this->source}',
    selector: '#{$this->btnId}',
    maxItem: {$maxItem}, 
    area: {$area},
    items: {$this->value()},
    placeholder: '{$this->placeholder()}',
    showCloseButton: false,
    closeButtonText: '{$closeLabel}',
    exceedMaxItemTip: '{$lessThenLabel}',
    selectedOptionsTip: '{$selectedOptionsLabel}',
    disabled: '{$disabled}',
    displayer: 'navList',
    \$displayerContainer: $('#{$containerId}'),
});
JS
        );
    }

    protected function setupStyle()
    {
        if (! $this->maxItem || $this->maxItem > 1) {
            $primayDark = Color::primarydark();

            Admin::style(
                ".select-resource .nav li a{padding:8px 10px;font-size:13px;font-weight:bold;color:{$primayDark}}.select-resource .nav li a.red{cursor:pointer}.select-resource .nav-stacked>li{border-bottom:1px solid #eee;background: #fff;}.select-resource .nav {border: 1px solid #eee;margin-bottom:5px;}"
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $this->btnId = $this->id.'-select-resource';

        $this->formatOptions();
        $this->formatValue();
        $this->setDefaultSource();
        $this->setupStyle();
        $this->setupScript();

        $name = $this->elementName ?: $this->formatName($this->column);

        $this->prepend('<i class="fa fa-long-arrow-up"></i>')
            ->defaultAttribute('class', 'form-control '.$this->elementClassString())
            ->defaultAttribute('type', 'text')
            ->defaultAttribute('id', $this->id.$this->formElementId())
            ->defaultAttribute('name', $name);

        $this->addVariables([
            'className'   => str_replace(['[', ']'], '_', $name),
            'prepend'     => $this->prepend,
            'append'      => $this->append,
            'placeholder' => $this->placeholder(),
            'style'       => $this->style,
            'btnId'       => $this->btnId,
        ]);

        return parent::render();
    }
}
