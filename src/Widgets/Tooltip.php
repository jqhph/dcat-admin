<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Admin;
use Illuminate\Contracts\Support\Renderable;

class Tooltip extends Widget
{
    protected static $style = '.tooltip-inner{padding:7px 13px;border-radius:2px;font-size:13px;max-width:250px}';

    protected $selector;

    protected $background;

    protected $maxWidth;

    protected $options = [
        'title'     => '',
        'html'      => true,
        'placement' => 'top',
    ];

    protected $built;

    public function __construct(string $selector = '')
    {
        $this->selector($selector);

        $this->autoRender();
    }

    public function selector(string $selector)
    {
        $this->selector = $selector;

        return $this;
    }

    public function maxWidth(string $width)
    {
        $this->maxWidth = $width;

        return $this;
    }

    /**
     * @param string|Renderable|\Closure $content
     *
     * @return $this
     */
    public function title($content)
    {
        $this->options['title'] = $this->toString($content);

        return $this;
    }

    public function background(string $color)
    {
        $this->background = $color;

        return $this;
    }

    public function green()
    {
        return $this->background(Admin::color()->success());
    }

    public function blue()
    {
        return $this->background(Admin::color()->blue());
    }

    public function red()
    {
        return $this->background(Admin::color()->danger());
    }

    public function purple()
    {
        return $this->background(Admin::color()->purple());
    }

    public function left()
    {
        return $this->placement('left');
    }

    public function right()
    {
        return $this->placement('right');
    }

    public function top()
    {
        return $this->placement('top');
    }

    public function bottom()
    {
        return $this->placement('bottom');
    }

    public function placement(string $val = 'left')
    {
        $this->options['placement'] = $val;

        return $this;
    }

    protected function setupStyle()
    {
        Admin::style(static::$style);
        if ($this->maxWidth) {
            Admin::style(".tooltip-inner{max-width:{$this->maxWidth}}");
        }
    }

    protected function setupScript()
    {
        $opts = json_encode($this->options, JSON_UNESCAPED_UNICODE);

        Admin::script("$('{$this->selector}').tooltip({$opts});");
    }

    public function render()
    {
        if ($this->built) {
            return;
        }
        $this->built = true;

        $background = $this->background ?: Admin::color()->primary();

        $this->defaultHtmlAttribute('class', 'tooltip-inner');
        $this->style('background:'.$background, true);

        $this->options['template'] =
            "<div class='tooltip' role='tooltip'><div class='tooltip-arrow' style='border-{$this->options['placement']}-color:{$background}'></div><div {$this->formatHtmlAttributes()}></div></div>";

        $this->setupStyle();
        $this->setupScript();
    }
}
