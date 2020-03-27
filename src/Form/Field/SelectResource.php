<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;
use Dcat\Admin\Form\Field;
use Dcat\Admin\SimpleGrid;
use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Arrayable;

class SelectResource extends Field
{
    use PlainInput;

    protected static $js = [
        '@resource-selector',
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

    protected function prepareInputValue($value)
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
        $disabled = empty($this->attributes['disabled']) ? '' : 'disabled';
        $containerId = $this->id.$this->getFormElementId();
        $maxItem = (int) $this->maxItem;
        $queryName = SimpleGrid::QUERY_NAME;

        Admin::script(
            <<<JS
Dcat.ResourceSelector({
    title: '{$label}',
    column: "{$this->getElementName()}",
    source: '{$this->source}',
    selector: '#{$this->btnId}',
    maxItem: {$maxItem}, 
    area: {$area},
    queryName: '{$queryName}',
    items: {$this->value()},
    placeholder: '{$this->placeholder()}',
    showCloseButton: false,
    disabled: '{$disabled}',
    displayer: 'navList',
    displayerContainer: $('#{$containerId}'),
});
JS
        );
    }

    protected function setupStyle()
    {
        if (! $this->maxItem || $this->maxItem > 1) {
            $primayDarker = Admin::color()->primaryDarker();

            Admin::style(
                ".select-resource .nav li{width:100%}.select-resource .nav li a{padding:8px 10px;font-size:13px;color:{$primayDarker}}.select-resource .nav li a.red{cursor:pointer}.select-resource .nav-stacked>li{border-bottom:1px solid #eee;background: #fff;}.select-resource .nav {border: 1px solid #eee;margin-bottom:5px;}"
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

        $this->prepend('<i class="feather icon-arrow-up"></i>')
            ->defaultAttribute('class', 'form-control '.$this->getElementClassString())
            ->defaultAttribute('type', 'text')
            ->defaultAttribute('id', $this->id.$this->getFormElementId())
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
