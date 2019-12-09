<?php

namespace Dcat\Admin\Form;

use Dcat\Admin\Form;
use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
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
        $this->form = $form;
        $this->builder = $callback;
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
     *
     * @return $this|string
     */
    public function title($title = null)
    {
        if ($title === null) {
            return $this->title;
        }

        $this->title = value($title);

        return $this;
    }

    /**
     * @param string|\Closure|Renderable $contents
     *
     * @return $this
     */
    public function content($contents)
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * @return string
     */
    public function elementId(): string
    {
        return $this->elementId;
    }

    /**
     * @return array
     */
    public function newId()
    {
        return $this->form->key();
    }

    /**
     * @param string|null $key
     * @param mixed|null  $default
     *
     * @return array|mixed
     */
    public function input($key = null, $default = null)
    {
        $input = $this->form->updates();

        if ($key === null) {
            return $input;
        }

        return Arr::get($input, $key, $default);
    }

    /**
     * @return $this
     */
    public function finish()
    {
        $value = call_user_func($this->builder, $this);

        if ($value) {
            $this->content($value);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        return Helper::render($this->contents);
    }
}
