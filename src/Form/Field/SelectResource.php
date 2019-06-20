<?php

namespace Dcat\Admin\Form\Field;

use Dcat\Admin\Admin;
use Dcat\Admin\Form\Field;
use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\URL;

class SelectResource extends Field
{
    use PlainInput;

    protected static $js = [
        'vendor/dcat-admin/dcat-admin/select-resource.min.js'
    ];

    protected $area = ['60%', '68%'];

    protected $source;

    protected $maxItem = 1;

    protected $style = 'primary';

    /**
     * Set window's area.
     *
     * @param string $width
     * @param string $height
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
        $opts = $this->options;
        if (is_callable($opts)) {
            $value = Helper::array(old($this->column, $this->value));

            $opts = call_user_func($opts, $value);
        }

        $this->options = Helper::array($opts);

    }

    /**
     * Multiple select.
     *
     * @param int|null|null $max
     * @return SelectResource
     */
    public function multiple(?int $max = null)
    {
        return $this->max($max);
    }

    /**
     *
     * @param ?int $max
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
     * @param $source
     * @return $this
     */
    public function path($source)
    {
        if ($source && URL::isValidUrl($source)) {
            $this->source = $source;
        } else {
            $this->source = admin_base_path($source);
        }

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

        $this->value = json_encode((object)$this->value);
    }

    protected function setDefaultSource()
    {
        if (!$this->source) {
            if (strpos($this->column, '.')) {
                $this->path(str_replace('_id', '', last(explode('.', $this->column))));
            } else {
                $this->path(str_replace('_id', '', $this->column));
            }
        }
    }

    public function prepare($value)
    {
        if ($this->maxItem == 1) {
            if ($value === null || $value === '') {
                return 0;
            }
            return $value;
        }

        return Helper::array($value, true);
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $this->formatOptions();
        $this->formatValue();
        $this->setDefaultSource();

        if (!$this->maxItem || $this->maxItem > 1) {
            Admin::style('.select-resource .nav li a{padding:8px 10px;font-size:13px;font-weight:bold;color:var(--primary-dark)}.select-resource .nav li a.red{cursor:pointer}.select-resource .nav-stacked>li{border-bottom:1px solid #eee;background: #fff;}.select-resource .nav {border: 1px solid #eee;margin-bottom:5px;}');
        }

        $this->defaultAttribute('class', 'form-control '.$this->getElementClassString());

        $name = $this->elementName ?: $this->formatName($this->column);

        $this->prepend('<i class="fa fa-long-arrow-up"></i>')
            ->defaultAttribute('type', 'text')
            ->defaultAttribute('id', $this->id.$this->getFormId())
            ->defaultAttribute('name', $name);

        $this->addVariables([
            'className'   => str_replace(['[', ']'], '_', $name),
            'prepend'     => $this->prepend,
            'append'      => $this->append,
            'area'        => json_encode($this->area),
            'maxItem'     => $this->maxItem,
            'source'      => $this->source,
            'placeholder' => $this->getPlaceholder(),
            'style'       => $this->style,
            'disabled'    => empty($this->attributes['disabled']) ? '' : 'disabled',

            'inputContainerId' => $this->id.$this->getFormId(),
        ]);

        return parent::render();
    }
}
