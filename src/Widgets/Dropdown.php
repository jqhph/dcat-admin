<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Admin;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Dropdown extends Widget
{
    const DIVIDER = '_divider';

    protected static $dividerHtml = '<li class="divider"></li>';

    public $template = '<span class="dropdown" style="display:inline-block">%s<ul class="dropdown-menu">%s</ul></span>';

    /**
     * @var array
     */
    protected $button = [
        'text'  => null,
        'class' => 'btn btn-sm btn-default',
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
     * @var array
     */
    protected $firstOptions = [];

    public function __construct(array $options = [])
    {
        $this->options($options);
    }

    public function options($options = [], string $title = null)
    {
        if (!$options) return $this;

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        $options = (array)$options;

        if (!$this->options) {
            $this->firstOptions = &$options;
        }

        $this->options[] = [$title, &$options];

        return $this;
    }

    public function button(?string $text)
    {
        $this->button['text'] = $text;

        return $this;
    }

    public function withoutTextButton()
    {
        return $this->button('');
    }

    public function buttonClass(string $class)
    {
        $this->button['class'] = $class;

        return $this;
    }

    public function buttonStyle(string $style)
    {
        $this->button['style'] = $style;

        return $this;
    }

    public function divider()
    {
        $this->divider = true;

        return $this;
    }

    public function map(\Closure $builder)
    {
        $this->builder = $builder;

        return $this;
    }

    public function click(?string $defaultLabel = null)
    {
        $this->click = true;

        $this->buttonId = 'dropd_'.Str::random(8);

        if ($defaultLabel !== null) {
            $this->button($defaultLabel);
        }

        return $this;
    }

    public function template($template)
    {
        $this->template = $this->toString($template);

        return $this;
    }

    protected function renderButton()
    {
        if (is_null($this->button['text']) && !$this->click) return;

        $text  = $this->button['text'];
        $class = $this->button['class'];
        $style = $this->button['style'];

        if ($this->click && !$text) {
            if (Arr::isAssoc($this->firstOptions)) {
                $text = array_keys($this->firstOptions)[0];
            } else {
                $text = $this->firstOptions[0] ?? '';
            }

            if (is_array($text)) {
                $text = $text['label'] ?? current($text);
            }
        }

        return str_replace(
            ['{id}', '{class}', '{style}', '{text}'],
            [
                $this->buttonId,
                $class,
                $style ? "style='$style'" : '',
                $text ? " $text &nbsp;" : ''
            ],
            <<<'HTML'
<a id="{id}" class="{class} dropdown-toggle " data-toggle="dropdown" href="javascript:void(0)" {style}>
    <stub>{text}</stub>
    <span class="caret"></span>
</a>
HTML
        );
    }

    public function getButtonId()
    {
        return $this->buttonId;
    }

    protected function renderOptions()
    {
        $opt = '';

        foreach ($this->options as &$items) {
            list($title, $options) = $items;

            if ($title) {
                $opt .= "<li class='dropdown-header'>$title</li>";
            }

            foreach ($options as $key => $val) {
                $opt .= $this->renderOption($key, $val);
            }
        }

        return $opt;
    }

    protected function renderOption($k, $v)
    {
        if ($v === static::DIVIDER) {
            return static::$dividerHtml;
        }

        if ($builder = $this->builder) {
            $v = $builder->call($this, $v, $k);
        }

        $v = strpos($v, '</a>') ? $v : "<a href='javascript:void(0)'>$v</a>";
        $v = "<li>$v</li>";

        if ($this->divider) {
            $v .= static::$dividerHtml;
            $this->divider = null;
        }

        return $v;
    }

    public function render()
    {
        if (is_null($this->button['text']) && !$this->options) {
            return '';
        }

        $button = $this->renderButton();

        if (!$this->options) {
            return $button;
        }

        $opt = $this->renderOptions();

        if (!$button) {
            return sprintf('<ul class="dropdown-menu">%s</ul>', $opt);
        }

        $label = $this->button['text'];

        if ($this->click) {
            Admin::script(
                <<<JS
(function () {
    var btn = $('#{$this->buttonId}'), _a = btn.parent().find('ul li a'), text = '$label';                
    _a.click(function () {
        btn.find('stub').html($(this).html() + ' &nbsp;');
    });
    if (text) {
        btn.find('stub').html(text + ' &nbsp;');
    } else {
        (!_a.length) || btn.find('stub').html($(_a[0]).html() + ' &nbsp;');
    }
})();
JS
            );
        }

        return sprintf(
            $this->template,
            $button,
            $opt
        );
    }

}
