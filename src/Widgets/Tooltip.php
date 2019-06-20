<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Admin;

class Tooltip extends Widget
{
    public static $style = '.tooltip-inner{padding:7px 13px;border-radius:1px;font-size:13px;max-width:250px}';

    protected $selector;

    protected $bg = 'var(--primary)';

    protected $maxWidth;

    protected $options = [
        'title'     => '',
        'html'      => true,
        'placement' => 'top',
    ];

    public function __construct($selector = null)
    {
        $this->selector($selector);
    }

    /**
     * @param $selector
     * @return $this
     */
    public function selector($selector)
    {
        $this->selector = $selector;

        return $this;
    }

    /**
     * Set max width for tooltip.
     *
     * @param string $width
     * @return $this
     */
    public function maxWidth(string $width)
    {
        $this->maxWidth = $width;

        return $this;
    }

    /**
     * Set tooltip content.
     *
     * @param $content
     * @return $this
     */
    public function content($content)
    {
        $this->options['title'] = $this->toString($content);

        return $this;
    }

    /**
     * Set the backgroud of tooltip.
     *
     * @param string $color
     * @return $this
     */
    public function background(string $color)
    {
        $this->bg = $color;
        return $this;
    }

    public function green()
    {
        return $this->background('var(--success)');
    }

    public function blue()
    {
        return $this->background('var(--blue)');
    }

    public function red()
    {
        return $this->background('var(--danger)');
    }

    public function purple()
    {
        return $this->background('var(--purple)');
    }

    /**
     * Tooltip on left.
     *
     * @return $this
     */
    public function left()
    {
        return $this->placement('left');
    }

    /**
     * Tooltip on right.
     *
     * @return $this
     */
    public function right()
    {
        return $this->placement('right');
    }

    /**
     * Tooltip on top.
     *
     * @return $this
     */
    public function top()
    {
        return $this->placement('top');
    }

    /**
     * Tooltip on bottom.
     *
     * @return $this
     */
    public function bottom()
    {
        return $this->placement('bottom');
    }

    /**
     * How to position the tooltip - top | bottom | left | right.
     *
     * @param string $val
     * @return $this
     */
    public function placement(string $val = 'left')
    {
        $this->options['placement'] = $val;

        return $this;
    }

    public function render()
    {
        if ($style = static::$style) {
            static::$style = '';

            Admin::style($style);
        }
        if ($this->maxWidth) {
            Admin::style(".tooltip-inner{max-width:{$this->maxWidth}}");
        }

        $this->defaultAttribute('class', 'tooltip-inner');
        $this->style('background:'.$this->bg, true);

        $this->options['template'] = "<div class='tooltip' role='tooltip'><div class='tooltip-arrow' style='border-{$this->options['placement']}-color:{$this->bg}'></div><div {$this->formatAttributes()}></div></div>";

        $opts = json_encode($this->options, JSON_UNESCAPED_UNICODE);

        Admin::script("$('{$this->selector}').tooltip({$opts});");

    }
}
