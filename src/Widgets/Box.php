<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Grid\LazyRenderable as LazyGrid;
use Illuminate\Contracts\Support\Renderable;

class Box extends Widget
{
    protected $view = 'admin::widgets.box';
    protected $title = 'Box header';
    protected $content = 'here is the box content.';
    protected $tools = [];
    protected $padding;

    public function __construct($title = '', $content = '')
    {
        if ($title) {
            $this->title($title);
        }

        if ($content) {
            $this->content($content);
        }

        $this->class('box');
    }

    /**
     * Set content padding.
     *
     * @param  string  $padding
     */
    public function padding(string $padding)
    {
        $this->padding = 'padding:'.$padding;

        return $this;
    }

    /**
     * Set box content.
     *
     * @param  string  $content
     * @return $this
     */
    public function content($content)
    {
        if ($content instanceof LazyGrid) {
            $content->simple();
        }

        $this->content = $this->formatRenderable($content);

        return $this;
    }

    /**
     * Set box title.
     *
     * @param  string  $title
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set box as collapsable.
     *
     * @return $this
     */
    public function collapsable()
    {
        $this->tools[] =
            '<button class="border-0 bg-white" data-action="collapse"><i class="feather icon-minus"></i></button>';

        return $this;
    }

    /**
     * Set box as removable.
     *
     * @return $this
     */
    public function removable()
    {
        $this->tools[] =
            '<button class="border-0 bg-white" data-action="remove"><i class="feather icon-x"></i></button>';

        return $this;
    }

    /**
     * Set box style.
     *
     * @param  string  $styles
     * @return $this|Box
     */
    public function style($styles)
    {
        $styles = array_map(function ($style) {
            return 'box-'.$style;
        }, (array) $styles);

        $this->class = $this->class.' '.implode(' ', $styles);

        return $this;
    }

    /**
     * @param  string|Renderable|\Closure  $content
     * @return $this
     */
    public function tool($content)
    {
        $this->tools[] = $this->toString($content);

        return $this;
    }

    /**
     * Add `box-solid` class to box.
     *
     * @return $this
     */
    public function solid()
    {
        return $this->style('solid');
    }

    /**
     * Variables in view.
     *
     * @return array
     */
    public function defaultVariables()
    {
        return [
            'title'      => $this->title,
            'content'    => $this->toString($this->content),
            'tools'      => $this->tools,
            'attributes' => $this->formatHtmlAttributes(),
            'padding'    => $this->padding,
        ];
    }
}
