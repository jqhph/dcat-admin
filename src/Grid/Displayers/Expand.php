<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Support\Helper;
use Illuminate\Support\Str;

class Expand extends AbstractDisplayer
{
    protected $button;

    protected static $counter = 0;

    public function button($button)
    {
        $this->button = $button;
    }

    public function display($callbackOrButton = null)
    {
        $html = $this->value;
        $remoteUrl = '';

        if ($callbackOrButton && $callbackOrButton instanceof \Closure) {
            $callbackOrButton = $callbackOrButton->call($this->row, $this);

            if (! $callbackOrButton instanceof LazyRenderable) {
                $html = Helper::render($callbackOrButton);

                $callbackOrButton = null;
            }
        }

        if ($callbackOrButton instanceof LazyRenderable) {
            $html = '<div style="min-height: 150px"></div>';

            $remoteUrl = $callbackOrButton->getUrl();
        } elseif (is_string($callbackOrButton) && is_subclass_of($callbackOrButton, LazyRenderable::class)) {
            $html = '<div style="min-height: 150px"></div>';

            $renderable = $callbackOrButton::make();

            $remoteUrl = $renderable->getUrl();
        } elseif ($callbackOrButton && is_string($callbackOrButton)) {
            $this->button = $callbackOrButton;
        }

        $button = is_null($this->button) ? $this->value : $this->button;

        return Admin::view('admin::grid.displayer.expand', [
            'key'     => $this->getKey(),
            'url'     => $remoteUrl,
            'button'  => $button,
            'html'    => $html,
            'dataKey' => $this->getDataKey(),
        ]);
    }

    protected function getDataKey()
    {
        $key = $this->getKey() ?: Str::random(8);

        static::$counter++;

        return $this->grid->makeName($key.'-'.static::$counter);
    }
}
