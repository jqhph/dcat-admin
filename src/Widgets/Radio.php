<?php

namespace Dcat\Admin\Widgets;

use Illuminate\Contracts\Support\Arrayable;

class Radio extends Widget
{
    protected $view = 'admin::widgets.radio';
    protected $type = 'radio';
    protected $style = 'primary';
    protected $right = '16px';
    protected $checked;
    protected $disabledValues = [];
    protected $size;
    protected $inline = false;

    public function __construct(
        ?string $name = null,
        array $options = [],
        string $style = 'primary'
    ) {
        $this->name($name);
        $this->options($options);
        $this->style($style);
    }

    /**
     * 设置表单 "name" 属性.
     *
     * @param string $name
     *
     * @return $this
     */
    public function name(?string $name)
    {
        return $this->setHtmlAttribute('name', $name);
    }

    /**
     * 设置为小尺寸.
     *
     * @return $this
     */
    public function small()
    {
        return $this->size('sm');
    }

    /**
     * 设置为大尺寸.
     *
     * @return $this
     */
    public function large()
    {
        return $this->size('lg');
    }

    /**
     * 尺寸设置.
     *
     * "sm", "lg"
     *
     * @param string $size
     *
     * @return $this
     */
    public function size(string $size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * 是否排成一行.
     *
     * @param bool $inine
     *
     * @return $this
     */
    public function inline(bool $inine = true)
    {
        $this->inline = $inine;

        return $this;
    }

    /**
     * 设置禁选的选项.
     *
     * @param string|array $values
     *
     * @return $this
     */
    public function disable($values = null)
    {
        if ($values) {
            $this->disabledValues = (array) $values;

            return $this;
        }

        return $this->setHtmlAttribute('disabled', 'disabled');
    }

    /**
     * 设置 "margin-right" 样式.
     *
     * @param string $value
     *
     * @return $this
     */
    public function right(string $value)
    {
        $this->right = $value;

        return $this;
    }

    /**
     * 设置选中的选项.
     *
     * @param string $id
     *
     * @return $this
     */
    public function check($option)
    {
        $this->checked = $option;

        return $this;
    }

    /**
     * 设置选项的名称和值.
     *
     * eg: $opts = [
     *         1 => 'foo',
     *         2 => 'bar',
     *         ...
     *     ]
     *
     * @param array $opts
     *
     * @return $this
     */
    public function options($opts = [])
    {
        if ($opts instanceof Arrayable) {
            $opts = $opts->toArray();
        }
        $this->options = $opts;

        return $this;
    }

    /**
     * 设置样式.
     *
     * 支持 "info", "primary", "danger", "success".
     *
     * @param string $style
     *
     * @return $this
     */
    public function style(string $style)
    {
        $this->style = $style;

        return $this;
    }

    /**
     * @return array
     */
    public function defaultVariables()
    {
        return [
            'style'      => $this->style,
            'options'    => $this->options,
            'attributes' => $this->formatHtmlAttributes(),
            'checked'    => $this->checked,
            'disabled'   => $this->disabledValues,
            'right'      => $this->right,
            'size'       => $this->size,
            'inline'     => $this->inline,
        ];
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->setHtmlAttribute('type', $this->type);

        return parent::render();
    }
}
