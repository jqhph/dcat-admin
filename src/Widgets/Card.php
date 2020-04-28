<?php

namespace Dcat\Admin\Widgets;

use Illuminate\Contracts\Support\Renderable;

class Card extends Widget
{
    /**
     * @var string
     */
    protected $view = 'admin::widgets.card';

    protected $title;

    protected $content;

    protected $footer;

    /**
     * @var array
     */
    protected $tools = [];

    /**
     * @var bool
     */
    protected $divider = false;

    public function __construct($title = '', $content = null)
    {
        if ($content === null) {
            $content = $title;
            $title = '';
        }

        if ($title) {
            $this->title($title);
        }

        if ($content !== null) {
            $this->content($content);
        }

        $this->class('card');
    }

    /**
     * @return $this
     */
    public function withHeaderBorder()
    {
        $this->divider = true;

        return $this;
    }

    /**
     * 设置卡片间距.
     *
     * @param string $padding
     */
    public function padding(string $padding)
    {
        $this->padding = 'padding:'.$padding;

        return $this;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function content($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function footer($content)
    {
        $this->footer = $content;

        return $this;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param string|Renderable|\Closure $content
     *
     * @return $this
     */
    public function tool($content)
    {
        $this->tools[] = $this->toString($content);

        return $this;
    }

    /**
     * Variables in view.
     *
     * @return array
     */
    public function variables()
    {
        return [
            'title'      => $this->title,
            'content'    => $this->toString($this->content),
            'footer'     => $this->toString($this->footer),
            'tools'      => $this->tools,
            'attributes' => $this->formatHtmlAttributes(),
            'padding'    => $this->padding,
            'divider'    => $this->divider,
        ];
    }
}
