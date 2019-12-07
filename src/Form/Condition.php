<?php

namespace Dcat\Admin\Form;

use Dcat\Admin\Form;

/**
 * @mixin Form
 */
class Condition
{
    /**
     * @var Form
     */
    protected $form;

    protected $done = false;

    protected $condition;

    protected $result;

    /**
     * @var \Closure[]
     */
    protected $next = [];

    public function __construct($condition, Form $form)
    {
        $this->condition = $condition;
        $this->form = $form;
    }

    public function then(\Closure $closure)
    {
        $this->next[] = $closure;

        return $this;
    }

    public function now(\Closure $next = null)
    {
        $this->process($next);
    }

    public function else(\Closure $next = null)
    {
        $self = $this;

        $condition = $this->form->if(function () use ($self) {
            return ! $self->getResult();
        });

        if ($next) {
            $condition->then($next);
        }

        return $condition;
    }

    public function process(\Closure $next = null)
    {
        if ($this->done) {
            return;
        }
        $this->done = true;

        if (! $this->is()) {
            return;
        }

        if ($next) {
            $this->then($next);
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

        return $this->result = $this->condition ? true : false;
    }

    public function getResult()
    {
        return $this->result;
    }

    protected function call(\Closure $callback)
    {
        return $callback($this->form);
    }

    public function __call($name, $arguments)
    {
        if ($name == 'if') {
            return $this->form->if(...$arguments);
        }

        return $this->then(function (Form $form) use ($name, &$arguments) {
            return $form->$name(...$arguments);
        });
    }
}
