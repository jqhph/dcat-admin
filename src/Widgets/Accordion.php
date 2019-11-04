<?php

namespace Dcat\Admin\Widgets;

use Illuminate\Support\Str;

class Accordion extends Widget
{
    /**
     * @var string
     */
    protected $view = 'admin::widgets.accordion';

    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var string
     */
    protected $panelStyle = 'panel-default';

    /**
     * Collapse constructor.
     */
    public function __construct()
    {
        $this->class('panel-group');
        $this->id('accordion-'.Str::random(8));
    }

    /**
     * @return $this
     */
    public function white()
    {
        return $this->panelStyle('white');
    }

    /**
     * @return $this
     */
    public function panelStyle(string $style)
    {
        $this->panelStyle = 'panel-'.$style;

        return $this;
    }

    /**
     * Add item.
     *
     * @param string $title
     * @param string $content
     *
     * @return $this
     */
    public function add($title, $content, bool $expand = false)
    {
        $this->items[] = [
            'id'      => 'accordion-'.Str::random(12),
            'title'   => $title,
            'content' => $this->toString($content),
            'expand'  => $expand,
        ];

        return $this;
    }

    public function variables()
    {
        return [
            'id'         => $this->getHtmlAttribute('id'),
            'items'      => $this->items,
            'panelStyle' => $this->panelStyle,
            'attributes' => $this->formatHtmlAttributes(),
        ];
    }
}
