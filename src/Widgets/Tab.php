<?php

namespace Dcat\Admin\Widgets;

use Illuminate\Contracts\Support\Renderable;

class Tab extends Widget
{
    const TYPE_CONTENT = 1;
    const TYPE_LINK = 2;

    /**
     * @var string
     */
    protected $view = 'admin::widgets.tab';

    /**
     * @var array
     */
    protected $data = [
        'id'       => '',
        'title'    => '',
        'tabs'     => [],
        'dropDown' => [],
        'active'   => 0,
        'padding'  => null,
        'tabStyle' => '',
    ];

    /**
     * Add a tab and its contents.
     *
     * @param string            $title
     * @param string|Renderable $content
     * @param bool              $active
     *
     * @return $this
     */
    public function add($title, $content, $active = false)
    {
        $this->data['tabs'][] = [
            'id'      => mt_rand(),
            'title'   => $title,
            'content' => $this->toString($this->formatRenderable($content)),
            'type'    => static::TYPE_CONTENT,
        ];

        if ($active) {
            $this->data['active'] = count($this->data['tabs']) - 1;
        }

        return $this;
    }

    /**
     * Add a link on tab.
     *
     * @param string $title
     * @param string $href
     * @param bool   $active
     *
     * @return $this
     */
    public function addLink($title, $href, $active = false)
    {
        $this->data['tabs'][] = [
            'id'      => mt_rand(),
            'title'   => $title,
            'href'    => $href,
            'type'    => static::TYPE_LINK,
        ];

        if ($active) {
            $this->data['active'] = count($this->data['tabs']) - 1;
        }

        return $this;
    }

    /**
     * Set tab content padding.
     *
     * @param string $padding
     */
    public function padding(string $padding)
    {
        $this->data['padding'] = 'padding:'.$padding;

        return $this;
    }

    public function noPadding()
    {
        return $this->padding('0');
    }

    /**
     * Set title.
     *
     * @param string $title
     */
    public function title($title = '')
    {
        $this->data['title'] = $title;

        return $this;
    }

    /**
     * Set drop-down items.
     *
     * @param array $links
     *
     * @return $this
     */
    public function dropdown(array $links)
    {
        if (is_array($links[0])) {
            foreach ($links as $link) {
                call_user_func([$this, 'dropDown'], $link);
            }

            return $this;
        }

        $this->data['dropDown'][] = [
            'name' => $links[0],
            'href' => $links[1],
        ];

        return $this;
    }

    public function withCard()
    {
        return $this
            ->class('card', true)
            ->style('padding:.25rem .4rem .4rem');
    }

    public function vertical()
    {
        return $this
            ->class('nav-vertical d-block', true)
            ->style('padding:0!important;')
            ->tabStyle('nav-left flex-column');
    }

    public function theme(string $style = 'primary')
    {
        return $this
            ->class('nav-theme-'.$style, true)
            ->style('padding:0!important;');
    }

    public function tabStyle($type)
    {
        $this->data['tabStyle'] = $type;

        return $this;
    }

    /**
     * Render Tab.
     *
     * @return string
     */
    public function render()
    {
        $data = array_merge(
            $this->data,
            ['attributes' => $this->formatHtmlAttributes()]
        );

        return view($this->view, $data)->render();
    }
}
