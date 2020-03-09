<?php

namespace Dcat\Admin\Form\Step;

use Dcat\Admin\Form;
use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Done
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
        $this->elementId = 'step-finish-'.Str::random();

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
    public function contents($contents)
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * @return string
     */
    public function getElementId(): string
    {
        return $this->elementId;
    }

    /**
     * @return array
     */
    public function getNewId()
    {
        return $this->form->getKey();
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
     * @return void
     */
    protected function callBuilder()
    {
        if (! $this->builder) {
            return;
        }

        if ($value = call_user_func($this->builder, $this)) {
            $this->contents($value);
        }
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->callBuilder();

        return Helper::render($this->contents);
    }
}
