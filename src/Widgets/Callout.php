<?php

namespace Dcat\Admin\Widgets;

use Illuminate\Contracts\Support\Renderable;

class Callout extends Widget
{
    /**
     * @var string
     */
    protected $view = 'admin::widgets.alert';

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $style = 'default';

    /**
     * @var bool
     */
    protected $showCloseBtn = false;

    /**
     * Alert constructor.
     *
     * @param mixed  $content
     * @param string $title
     * @param string $style
     */
    public function __construct($content = '', ?string $title = null, ?string $style = null)
    {
        $this->content($content);

        $this->title($title);

        $this->style($style);
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return $this
     */
    public function title(?string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set contents.
     *
     * @param string|\Closure|Renderable $content
     *
     * @return $this
     */
    public function content($content)
    {
        $this->content = $this->toString($content);

        return $this;
    }

    /**
     * Set light style.
     *
     * @return $this
     */
    public function light()
    {
        return $this->style('light');
    }

    /**
     * Set primary style.
     *
     * @return $this
     */
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
        return $this->style('info');
    }

    /**
     * Set success style.
     *
     * @return $this
     */
    public function success()
    {
        return $this->style('success');
    }

    /**
     * Set warning style.
     *
     * @return $this
     */
    public function warning()
    {
        return $this->style('warning');
    }

    /**
     * Set warning style.
     *
     * @return $this
     */
    public function danger()
    {
        return $this->style('danger');
    }

    /**
     * Show close button.
     *
     * @param bool $value
     *
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
     * @param string $style
     *
     * @return $this
     */
    public function style(?string $style = 'info')
    {
        $this->style = $style;

        return $this;
    }

    /**
     * @return array
     */
    public function variables()
    {
        $this->class("callout callout-{$this->style} alert alert-dismissable");

        return [
            'title'        => $this->title,
            'content'      => $this->content,
            'attributes'   => $this->formatHtmlAttributes(),
            'showCloseBtn' => $this->showCloseBtn,
        ];
    }
}
