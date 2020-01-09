<?php

namespace Dcat\Admin\Grid\Filter\Presenter;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;

class SelectResource extends Presenter
{
    public static $js = [
        'vendor/dcat-admin/dcat-admin/select-resource.min.js',
    ];

    /**
     * @var string
     */
    protected $placeholder = '';

    protected $area = ['51%', '65%'];

    protected $source;

    protected $maxItem = 1;

    /**
     * @var \Closure
     */
    protected $options;

    protected $value;

    protected $btnStyle = 'primary';

    public function __construct($source = null)
    {
        $this->path($source);
    }

    /**
     * @param string $width
     * @param string $height
     *
     * @return $this
     */
    public function area($width, $height)
    {
        $this->area = [$width, $height];

        return $this;
    }

    /**
     * Set the field options.
     *
     * @param \Closure $options
     *
     * @return $this
     */
    public function options(\Closure $options)
    {
        $this->options = $options;

        return $this;
    }

    protected function formatOptions()
    {
        $opts = $this->options;
        if (is_callable($opts)) {
            $opts = call_user_func($opts, Helper::array($this->value));
        }

        $this->options = Helper::array($opts);
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
        $this->btnStyle = $style;

        return $this;
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
        $value = Helper::array($this->value);

        $this->value = [];

        foreach ($this->options as $id => $label) {
            foreach ($value as $v) {
                if ($v == $id && $v !== null) {
                    $this->value[$v] = $label;
                }
            }
        }

        $this->filter->setValue(json_encode((object) $this->value));
    }

    /**
     * Set input placeholder.
     *
     * @param string $placeholder
     *
     * @return $this
     */
    public function placeholder($placeholder = '')
    {
        $this->placeholder = $placeholder;

        return $this;
    }

    protected function setDefaultSource()
    {
        if (! $this->source) {
            $column = $this->filter->column();
            if (strpos($column, '.')) {
                $this->path(str_replace('_id', '', last(explode('.', $column))));
            } else {
                $this->path(str_replace('_id', '', $column));
            }
        }
    }

    /**
     * @return array
     */
    public function variables(): array
    {
        $this->value = request(
            $this->filter->column(),
            $this->filter->getValue() ?: $this->filter->getDefault()
        );

        $this->formatOptions();
        $this->formatValue();
        $this->setDefaultSource();

        $containerClass = 'form-control';

        if (! $this->maxItem || $this->maxItem > 2) {
            // 选项大于两个时使用select2样式布局
            Admin::css(Admin::$componentsAssets['select2']['css']);

            $containerClass = 'select2 select2-container select2-container--default select2-container--below select2-container--focus ';
        }

        return [
            'area'           => json_encode($this->area),
            'maxItem'        => $this->maxItem,
            'source'         => $this->source,
            'placeholder'    => $this->placeholder,
            'containerClass' => $containerClass,
            'btnStyle'       => $this->btnStyle,
        ];
    }
}
