<?php

namespace Tests;

use Facebook\WebDriver\Exception\TimeoutException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Assert as PHPUnit;

trait BrowserExtension
{
    public function extendBrowser()
    {
        $this->extendBrowserWhenTextAvailable();
        $this->extendBrowserWhenElementAvailable();
        $this->extendBrowserWait();
        $this->extendBrowserAssertHasInput();
        $this->extendBrowserAssertHidden();
    }

    private function extendBrowserAssertHidden()
    {
        Browser::macro('assertHidden', function ($selector) {
            $fullSelector = $this->resolver->format($selector);

            PHPUnit::assertTrue(
                $this->resolver->findOrFail($selector)->isDisplayed(),
                "Element [{$fullSelector}] is visible."
            );

            return $this;
        });
    }

    private function extendBrowserWait()
    {
        $self = $this;

        Browser::macro('wait', function ($seconds, \Closure $callback = null) use ($self) {
            $delayBrowser = $self->makeDelayBrowser($this);

            try {
                $this->waitUsing($seconds, 200, function () {});
            } catch (TimeoutException $e) {
                $callback && $callback();

                $delayBrowser();
            }

            return $delayBrowser;
        });
    }

    private function extendBrowserAssertHasInput()
    {
        Browser::macro('hasInput', function ($field) {
            /* @var \Facebook\WebDriver\Remote\RemoteWebElement $element */
            $this->resolver->resolveForTyping($field);

            return $this;
        });
    }

    private function extendBrowserWhenElementAvailable()
    {
        $self = $this;

        Browser::macro('whenElementAvailable', function ($selector, $callbackOrSeconds = null, $seconds = null) use ($self) {
            /* @var Browser $this */

            $callback = null;
            if (is_callable($callbackOrSeconds)) {
                $callback = $callbackOrSeconds;
            } elseif (is_int($callbackOrSeconds)) {
                $seconds = $callbackOrSeconds;
            }

            $delayBrowser = $self->makeDelayBrowser($this);

            $this->waitFor($selector, $seconds)->with($selector, function ($value) use ($callback, $delayBrowser) {
                $callback && $callback($value);

                return $delayBrowser();
            });

            return $delayBrowser;
        });
    }

    private function extendBrowserWhenTextAvailable()
    {
        $self = $this;

        Browser::macro('whenTextAvailable', function ($text, $callbackOrSeconds = null, $seconds = null) use ($self) {
            $callback = null;

            if (is_callable($callbackOrSeconds)) {
                $callback = $callbackOrSeconds;
            } elseif (is_int($callbackOrSeconds)) {
                $seconds = $callbackOrSeconds;
            }

            $delayBrowser = $self->makeDelayBrowser($this);
            $text = Arr::wrap($text);
            $message = $this->formatTimeOutMessage('Waited %s seconds for text', implode("', '", $text));

            $this->waitUsing($seconds, 100, function () use ($text, $callback, $delayBrowser)  {
                $results = Str::contains($this->resolver->findOrFail('')->getText(), $text);

                if ($results) {
                    $callback && $callback($this);

                    $delayBrowser();
                }

                return $results;
            }, $message);

            return $delayBrowser;
        });
    }

    public function makeDelayBrowser($browser)
    {
        return new class($browser) {
            protected $browser;

            protected $callbacks = [];

            public function __construct(Browser $browser)
            {
                $this->browser = $browser;
            }

            public function __call($method, $arguments = [])
            {
                $this->callbacks[] = [
                    'method'    => $method,
                    'arguments' => $arguments,
                ];

                return $this;
            }

            public function __invoke()
            {
                $browser = $this->browser;

                foreach ($this->callbacks as $value) {
                    $method = $value['method'];

                    $browser = $browser->{$method}(...$value['arguments']);
                }

                return $browser;
            }
        };
    }
}
