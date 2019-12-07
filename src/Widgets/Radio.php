<?php

namespace Dcat\Admin\Widgets;

use Illuminate\Contracts\Support\Arrayable;

class Radio extends Widget
{
    protected $view = 'admin::widgets.radio';

    protected $type = 'radio';

    protected $style = 'primary';

    protected $checked;

    protected $inline = false;

    protected $disabledValues = [];

    public function __construct($name = null, array $options = [], $style = 'primary')
    {
        $this->name($name);
        $this->options($options);
        $this->style($style);
    }

    /**
     * @param null $options
     *
     * @return $this
     */
    public function disabled($options = null)
    {
        if ($options) {
            $this->disabledValues = (array) $options;

            return $this;
        }

        return $this->setHtmlAttribute('disabled', 'disabled');
    }

    public function name($name)
    {
        return $this->setHtmlAttribute('name', $name);
    }

    public function inline()
    {
        $this->inline = true;

        return $this;
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function checked($id)
    {
        $this->checked = $id;

        return $this;
    }

    /**
     * Set options.
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
     * "info", "primary", "inverse", "danger", "success", "purple".
     *
     * @param $v
     *
     * @return $this
     */
    public function style($v)
    {
        $this->style = $v;

        return $this;
    }

    public function variables()
    {
        return [
            'style'      => $this->style,
            'options'    => $this->options,
            'attributes' => $this->formatHtmlAttributes(),
            'checked'    => $this->checked,
            'inline'     => $this->inline ? $this->type.'-inline' : '',
            'disabled'   => $this->disabledValues,
        ];
    }

    public function render()
    {
        $this->setHtmlAttribute('type', $this->type);

        return parent::render();
    }
}
