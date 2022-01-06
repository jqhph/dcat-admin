<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Support\Helper;
use Illuminate\Support\Str;

class Dropdown extends Widget
{
    const DIVIDER = '_divider';

    /**
     * @var string
     */
    protected static $dividerHtml = '<li class="dropdown-divider"></li>';

    protected $view = 'admin::widgets.dropdown';

    /**
     * @var array
     */
    protected $button = [
        'text'  => null,
        'class' => 'btn btn-sm btn-white waves-effect',
        'style' => null,
    ];

    /**
     * @var string
     */
    protected $buttonId;

    /**
     * @var \Closure
     */
    protected $builder;

    /**
     * @var bool
     */
    protected $divider;

    /**
     * @var bool
     */
    protected $click = false;

    /**
     * @var string
     */
    protected $direction = 'down';

    public function __construct(array $options = [])
    {
        $this->options($options);
    }

    /**
     * Set the options of dropdown menus.
     *
     * @param  array  $options
     * @param  string|null  $title
     * @return $this
     */
    public function options($options = [], ?string $title = null)
    {
        if (! $options) {
            return $this;
        }

        $this->options[] = [$title, Helper::array($options)];

        return $this;
    }

    /**
     * Set the button text.
     *
     * @param  string|null  $text
     * @return $this
     */
    public function button(?string $text)
    {
        $this->button['text'] = $text;

        return $this;
    }

    /**
     * Set the button class.
     *
     * @param  string  $class
     * @return $this
     */
    public function buttonClass(?string $class)
    {
        $this->button['class'] = $class;

        return $this;
    }

    /**
     * Set the button style.
     *
     * @param  string  $class
     * @return $this
     */
    public function buttonStyle(?string $style)
    {
        $this->button['style'] = $style;

        return $this;
    }

    public function direction(string $direction = 'down')
    {
        $this->direction = $direction;

        return $this;
    }

    public function up()
    {
        return $this->direction('up');
    }

    public function down()
    {
        return $this->direction('down');
    }

    /**
     * Show divider.
     *
     * @param  string  $class
     * @return $this
     */
    public function divider()
    {
        $this->divider = true;

        return $this;
    }

    /**
     * Applies the callback to the elements of the options.
     *
     * @param  string  $class
     * @return $this
     */
    public function map(\Closure $builder)
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * Add click event listener.
     *
     * @param  string|null  $defaultLabel
     * @return $this
     */
    public function click(?string $defaultLabel = null)
    {
        $this->click = true;

        $this->buttonId = 'dropd-'.Str::random(8);

        if ($defaultLabel !== null) {
            $this->button($defaultLabel);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getButtonId()
    {
        return $this->buttonId;
    }

    /**
     * @return string
     */
    protected function renderOptions()
    {
        $html = '';

        foreach ($this->options as &$items) {
            [$title, $options] = $items;

            if ($title) {
                $html .= "<li class='dropdown-header'>$title</li>";
            }

            foreach ($options as $key => $val) {
                $html .= $this->renderOption($key, $val);
            }
        }

        return $html;
    }

    /**
     * @param  mixed  $k
     * @param  mixed  $v
     * @return mixed|string
     */
    protected function renderOption($k, $v)
    {
        if ($v === static::DIVIDER) {
            return static::$dividerHtml;
        }

        if ($builder = $this->builder) {
            $v = $builder->call($this, $v, $k);
        }

        $v = mb_strpos($v, '</a>') ? $v : "<a href='javascript:void(0)'>$v</a>";
        $v = "<li class='dropdown-item'>$v</li>";

        if ($this->divider) {
            $v .= static::$dividerHtml;
            $this->divider = null;
        }

        return $v;
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->addVariables([
            'options'   => $this->renderOptions(),
            'button'    => $this->button,
            'buttonId'  => $this->buttonId,
            'click'     => $this->click,
            'direction' => $this->direction,
        ]);

        return parent::render();
    }
}
