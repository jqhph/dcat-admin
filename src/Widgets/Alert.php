<?php

namespace Dcat\Admin\Widgets;

use Illuminate\Contracts\Support\Renderable;

class Alert extends Widget implements Renderable
{
    /**
     * @var string
     */
    protected $view = 'admin::widgets.alert';

    /**
     * @var string|\Symfony\Component\Translation\TranslatorInterface
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $content = '';

    /**
     * @var string
     */
    protected $style = 'danger';

    /**
     * @var string
     */
    protected $icon = 'ban';

    /**
     * @var bool
     */
    protected $showCloseBtn = true;

    /**
     * Alert constructor.
     *
     * @param mixed  $content
     * @param string $title
     * @param string $style
     */
    public function __construct($content = '', $title = null, $style = 'danger')
    {
        $this->content($content);

        $this->title($title);

        $this->style($style);
    }

    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    public function content($content)
    {
        $this->content = $this->toString($content);

        return $this;
    }

    public function info()
    {
        return $this->style('info')->icon('fa fa-info');
    }

    public function success()
    {
        return $this->style('success')->icon('fa fa-check');
    }

    public function warning()
    {
        return $this->style('warning')->icon('fa fa-warning');
    }

    public function disableCloseButton()
    {
        $this->showCloseBtn = false;

        return $this;
    }

    /**
     * Add style.
     *
     * @param string $style
     *
     * @return $this
     */
    public function style($style = 'info')
    {
        $this->style = $style;

        return $this;
    }

    /**
     * Add icon.
     *
     * @param string $icon
     *
     * @return $this
     */
    public function icon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return array
     */
    public function variables()
    {
        $this->class("alert alert-{$this->style} alert-dismissable");

        return [
            'title'        => $this->title,
            'content'      => $this->content,
            'icon'         => $this->icon,
            'attributes'   => $this->formatAttributes(),
            'showCloseBtn' => $this->showCloseBtn,
        ];
    }

}
