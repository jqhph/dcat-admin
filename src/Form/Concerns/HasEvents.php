<?php

namespace Dcat\Admin\Form\Concerns;

use Closure;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

trait HasEvents
{
    /**
     * @var array
     */
    protected $__hooks = [
        'creating'  => [],
        'editing'   => [],
        'submitted' => [],
        'saving'    => [],
        'saved'     => [],
        'deleting'  => [],
        'deleted'   => [],
    ];

    /**
     * Set after getting creating model callback.
     *
     * @param Closure $callback
     *
     * @return $this
     */
    public function creating(Closure $callback)
    {
        $this->__hooks['creating'][] = $callback;

        return $this;
    }

    /**
     * Set after getting editing model callback.
     *
     * @param Closure $callback
     *
     * @return $this
     */
    public function editing(Closure $callback)
    {
        $this->__hooks['editing'][] = $callback;

        return $this;
    }

    /**
     * Set submitted callback.
     *
     * @param Closure $callback
     *
     * @return $this
     */
    public function submitted(Closure $callback)
    {
        $this->__hooks['submitted'][] = $callback;

        return $this;
    }

    /**
     * Set saving callback.
     *
     * @param Closure $callback
     *
     * @return $this
     */
    public function saving(Closure $callback)
    {
        $this->__hooks['saving'][] = $callback;

        return $this;
    }

    /**
     * Set saved callback.
     *
     * @param Closure $callback
     *
     * @return $this
     */
    public function saved(Closure $callback)
    {
        $this->__hooks['saved'][] = $callback;

        return $this;
    }

    /**
     * @param Closure $callback
     *
     * @return $this
     */
    public function deleting(Closure $callback)
    {
        $this->__hooks['deleting'][] = $callback;

        return $this;
    }

    /**
     * @param Closure $callback
     *
     * @return $this
     */
    public function deleted(Closure $callback)
    {
        $this->__hooks['deleted'][] = $callback;

        return $this;
    }

    /**
     * Call creating callbacks.
     *
     * @return mixed
     */
    protected function callCreating()
    {
        return $this->callListeners('creating');
    }

    /**
     * Call editing callbacks.
     *
     * @return mixed
     */
    protected function callEditing()
    {
        return $this->callListeners('editing');
    }

    /**
     * Call submitted callback.
     *
     * @return mixed
     */
    protected function callSubmitted()
    {
        return $this->callListeners('submitted');
    }

    /**
     * Call saving callback.
     *
     * @return mixed
     */
    protected function callSaving()
    {
        return $this->callListeners('saving');
    }

    /**
     * Callback after saving a Model.
     *
     * @return mixed|null
     */
    protected function callSaved()
    {
        return $this->callListeners('saved');
    }

    /**
     * @return mixed|null
     */
    protected function callDeleting()
    {
        return $this->callListeners('deleting');
    }

    /**
     * @return mixed|null
     */
    protected function callDeleted()
    {
        return $this->callListeners('deleted');
    }

    /**
     * @param string $name
     *
     * @return RedirectResponse|\Illuminate\Http\Response|void
     */
    protected function callListeners($name)
    {
        $response = null;

        foreach ($this->__hooks[$name] as $func) {
            $this->model && $func->bindTo($this->model);

            $ret = $func($this);

            if (
                $response
                || ! $ret
                || ! $ret instanceof Response
                || ($ret instanceof RedirectResponse && $this->isAjaxRequest())
            ) {
                continue;
            }

            $response = $ret;
        }

        return $response;
    }
}
