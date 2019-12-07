<?php

namespace Dcat\Admin\Widgets;

use Illuminate\Contracts\Support\Renderable;

class Card extends Widget
{
    /**
     * @var string
     */
    protected $view = 'admin::widgets.card';

    /**
     * @var array
     */
    protected $tools = [];

    /**
     * @var bool
     */
    protected $divider = true;

    public function __construct($title = '', $content = '')
    {
        if ($title) {
            $this->title($title);
        }

        if ($content) {
            $this->content($content);
        }

        $this->class('card');
    }

    /**
     * Set content padding.
     *
     * @param string $padding
     */
    public function padding(string $padding)
    {
        $this->padding = 'padding:'.$padding;

        return $this;
    }

    /**
     * Set box content.
     *
     * @param string $content
     *
     * @return $this
     */
    public function content($content)
    {
        $this->content = $this->toString($content);

        return $this;
    }

    /**
     * Set box title.
     *
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
            'content'    => $this->content,
            'tools'      => $this->tools,
            'attributes' => $this->formatHtmlAttributes(),
            'style'      => $this->style,
            'padding'    => $this->padding,
        ];
    }
}
