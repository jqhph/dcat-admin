<?php

namespace Dcat\Admin\Widgets;

class Carousel extends Widget
{
    /**
     * @var string
     */
    protected $view = 'admin::widgets.carousel';

    /**
     * @var array
     */
    protected $items;

    /**
     * @var string
     */
    protected $title = 'Carousel';

    /**
     * Carousel constructor.
     *
     * @param array $items
     */
    public function __construct($items = [])
    {
        $this->items = $items;

        $this->id('carousel-'.uniqid());
        $this->class('carousel slide');
        $this->setHtmlAttribute('data-ride', 'carousel');
    }

    /**
     * Set title.
     *
     * @param string $title
     */
    public function title($title)
    {
        $this->title = $title;
    }

    /**
     * Render Carousel.
     *
     * @return string
     */
    public function render()
    {
        $variables = [
            'items'      => $this->items,
            'title'      => $this->title,
            'attributes' => $this->formatHtmlAttributes(),
            'id'         => $this->id,
        ];

        return view($this->view, $variables)->render();
    }
}
