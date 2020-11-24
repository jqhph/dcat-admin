<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;
use Dcat\Admin\Form\Field;
use Dcat\Admin\IFrameGrid;
use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @deprecated 即将在2.0版本中废弃
 */
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
        $value = Helper::array(old($this->column, $this->value()));

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
            if (mb_strpos($this->column, '.')) {
                $this->path(str_replace('_id', '', last(explode('.', $this->column))));
            } else {
                $this->path(str_replace('_id', '', $this->column));
            }
        }
    }

    protected function prepareInputValue($value)
    {
        if ($this->maxItem == 1) {
            return $value;
        }

        return Helper::array($value, true);
    }

    protected function setupScript()
    {
        $label = trans('admin.choose').' '.$this->label;
        $area = json_encode($this->area);
        $disabled = empty($this->attributes['disabled']) ? '' : 'disabled';
        $containerId = $this->id.$this->getFormElementId();
        $maxItem = (int) $this->maxItem;
        $queryName = IFrameGrid::QUERY_NAME;

        $displayerContainer = $this->isMultiple() ? "#{$containerId} .select2-selection" : "#{$containerId}";

        $this->script = <<<JS
Dcat.ResourceSelector({
    title: '{$label}',
    column: "{$this->getElementName()}",
    source: '{$this->source}',
    selector: replaceNestedFormIndex('#{$this->btnId}'),
    maxItem: {$maxItem}, 
    area: {$area},
    queryName: '{$queryName}',
    items: {$this->value()},
    placeholder: '{$this->placeholder()}',
    showCloseButton: false,
    disabled: '{$disabled}',
    displayer: 'default',
    displayerContainer: $(replaceNestedFormIndex('{$displayerContainer}')),
});
JS;
    }

    /**
     * {@inheritdoc}
     */
    public function placeholder($placeholder = null)
    {
        if ($placeholder === null) {
            return $this->placeholder ?: __('admin.choose').' '.$this->label;
        }

        $this->placeholder = $placeholder;

        return $this;
    }

    protected function setupStyle()
    {
        $containerClass = 'form-control';
        if ($this->isMultiple()) {
            // 选项大于两个时使用select2样式布局
            Admin::css('@select2');

            $containerClass = 'select2 select2-container select2-container--default select2-container--below ';
        }

        $this->attribute('class', "{$containerClass} {$this->getElementClassString()}");
    }

    public function isMultiple()
    {
        return ! $this->maxItem || $this->maxItem > 2;
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
            ->defaultAttribute('type', 'text')
            ->defaultAttribute('id', $this->id.$this->getFormElementId())
            ->defaultAttribute('name', $name);

        $this->addVariables([
            'className'   => str_replace(['[', ']'], '_', $name),
            'prepend'     => $this->prepend,
            'append'      => $this->append,
            'maxItem'     => $this->maxItem,
            'placeholder' => $this->placeholder(),
            'style'       => $this->style,
            'btnId'       => $this->btnId,
        ]);

        return parent::render();
    }
}
