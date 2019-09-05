<?php

namespace Dcat\Admin\Form;

use Dcat\Admin\Form;

class Condition
{
    /**
     * @var Form
     */
    protected $form;

    protected $done = false;

    protected $condition;

    /**
     * @var \Closure[]
     */
    protected $next = [];

    public function __construct($condition, Form $form)
    {
        $this->condition = $condition;
        $this->form = $form;
    }

    public function next(\Closure $closure)
    {
        $this->next[] = $closure;

        return $this;
    }

    public function then(\Closure $next = null)
    {
        if ($this->done) {
            return;
        }
        $this->done = true;

        if (! $this->is()) {
            return;
        }

        if ($next) {
            $this->call($next);
        }

        foreach ($this->next as $callback) {
            $this->call($callback);
        }
    }

    public function is()
    {
        if ($this->condition instanceof \Closure) {
            $this->condition = $this->call($this->condition);
        }

        return $this->condition ? true : false;
    }

    protected function call(\Closure $callback)
    {
        return $callback($this->form);
    }

    public function __call($name, $arguments)
    {
        if (! method_exists($this->form, $name)) {
            return $this;
        }

        return $this->next(function (Form $form) use ($name, &$arguments) {
            return $form->$name(...$arguments);
        });
    }

}
