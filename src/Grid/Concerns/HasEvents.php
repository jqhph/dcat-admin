<?php

namespace Dcat\Admin\Grid\Concerns;

use Dcat\Admin\Grid\Events;
use Illuminate\Support\Facades\Event;

trait HasEvents
{
    /**
     * @var array
     */
    protected $dispatched = [];

    /**
     * 监听事件.
     *
     * @param string $class
     *
     * @param \Closure $callback
     */
    public function listen(string $class, \Closure $callback)
    {
        Event::listen($class, function (Events\Event $event) use ($callback) {
            if ($event->grid !== $this) {
                return;
            }

            return $callback($event->grid, ...$event->payload);
        });
    }

    /**
     * 触发事件.
     *
     * @param \Dcat\Admin\Grid\Events\Event $event
     */
    public function fire(Events\Event $event)
    {
        $this->dispatched[get_class($event)] = $event;

        $event->setGrid($this);

        Event::dispatch($event);
    }

    /**
     * 只触发一次.
     *
     * @param \Dcat\Admin\Grid\Events\Event $event
     */
    public function fireOnce(Events\Event $event)
    {
        if (isset($this->dispatched[get_class($event)])) {
            return;
        }

        return $this->fire($event);
    }
}
