<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\Modal as WidgetModal;

class Modal extends AbstractDisplayer
{
    protected $title;

    public function title(string $title)
    {
        $this->title = $title;
    }

    protected function setUpLazyRenderable(LazyRenderable $renderable)
    {
        return clone $renderable->payload(['key' => $this->getKey()]);
    }

    public function display($callback = null)
    {
        $title = $this->value ?: $this->trans('title');
        if (func_num_args() == 2) {
            [$title, $callback] = func_get_args();
        }

        $html = $this->value;

        if ($callback instanceof \Closure) {
            $callback = $callback->call($this->row, $this);

            if (! $callback instanceof LazyRenderable) {
                $html = Helper::render($callback);

                $callback = null;
            }
        }

        if ($callback && is_string($callback) && is_subclass_of($callback, LazyRenderable::class)) {
            $html = $this->setUpLazyRenderable($callback::make());
        } elseif ($callback && $callback instanceof LazyRenderable) {
            $html = $this->setUpLazyRenderable($callback);
        }

        $title = $this->title ?: $title;

        return WidgetModal::make()
            ->lg()
            ->title($title)
            ->body($html)
            ->button($this->renderButton());
    }

    protected function renderButton()
    {
        return "<a href=\"javascript:void(0)\"><i class=\"fa fa-clone\"></i>&nbsp;&nbsp;{$this->value}</a>";
    }
}
