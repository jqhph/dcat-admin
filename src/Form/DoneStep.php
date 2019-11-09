<?php

namespace Dcat\Admin\Form;

use Dcat\Admin\Form;
use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;

class DoneStep
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $contents;

    /**
     * @var \Closure
     */
    protected $builder;

    /**
     * @var string
     */
    protected $elementId;

    public function __construct(Form $form, $title, \Closure $callback)
    {
        $this->form      = $form;
        $this->builder   = $callback;
        $this->elementId = 'done-step-'.Str::random();

        $this->title($title);
    }

    /**
     * @return Form
     */
    public function form()
    {
        return $this->form;
    }

    /**
     * @param string $title
     * @return void|string
     */
    public function title($title = null)
    {
        if ($title === null) {
            return $this->title;
        }

        $this->title = value($title);
    }

    /**
     * @param string|\Closure|Renderable $contents
     * @return void
     */
    public function contents($contents)
    {
        $this->contents = $contents;
    }

    /**
     * @return string
     */
    public function getElementId(): string
    {
        return $this->elementId;
    }

    /**
     * @return string
     */
    public function build()
    {
        call_user_func($this->builder, $this);
    }

}
