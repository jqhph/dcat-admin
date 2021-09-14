<?php

namespace Dcat\Admin\Widgets;

use Illuminate\Contracts\Support\Renderable;

class Alert extends Widget
{
    protected $view = 'admin::widgets.alert';
    protected $title;
    protected $content;
    protected $style;
    protected $icon;
    protected $showCloseBtn = false;

    public function __construct($content = '', $title = null, $style = 'danger')
    {
        $this->content($content);

        $this->title($title);

        $this->style($style);
    }

    /**
     * Set title.
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
     * Set contents.
     *
     * @param  string|\Closure|Renderable  $content
     * @return $this
     */
    public function content($content)
    {
        $this->content = $this->toString($content);

        return $this;
    }

    public function primary()
    {
        return $this->style('primary');
    }

    /**
     * Set info style.
     *
     * @return $this
     */
    public function info()
    {
        return $this->style('info')->icon('fa fa-info');
    }

    /**
     * Set success style.
     *
     * @return $this
     */
    public function success()
    {
        return $this->style('success')->icon('fa fa-check');
    }

    /**
     * Set warning style.
     *
     * @return $this
     */
    public function warning()
    {
        return $this->style('warning')->icon('fa fa-warning');
    }

    /**
     * Set warning style.
     *
     * @return $this
     */
    public function danger()
    {
        return $this->style('danger')->icon('fa fa-ban');
    }

    /**
     * Show close button.
     *
     * @param  bool  $value
     * @return $this
     */
    public function removable(bool $value = true)
    {
        $this->showCloseBtn = $value;

        return $this;
    }

    /**
     * Add style.
     *
     * @param  string  $style
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
     * @param  string  $icon
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
    public function defaultVariables()
    {
        $this->class("alert alert-{$this->style} alert-dismissable");

        return [
            'title'        => $this->title,
            'content'      => $this->content,
            'icon'         => $this->icon,
            'attributes'   => $this->formatHtmlAttributes(),
            'showCloseBtn' => $this->showCloseBtn,
        ];
    }
}
