<?php

namespace Tests;

use Facebook\WebDriver\Exception\TimeoutException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Component;
use PHPUnit\Framework\Assert as PHPUnit;

trait BrowserExtension
{
    public function extendBrowser()
    {
        $functions = [
            'whenTextAvailable' => function ($text, $callbackOrSeconds = null, $seconds = null) {
                $callback = null;

                if (is_callable($callbackOrSeconds)) {
                    $callback = $callbackOrSeconds;
                } elseif (is_numeric($callbackOrSeconds)) {
                    $seconds = $callbackOrSeconds;
                }

                $text = Arr::wrap($text);
                $message = $this->formatTimeOutMessage('Waited %s seconds for text', implode("', '", $text));

                return $this->waitUsing($seconds, 100, function () use ($text, $callback)  {
                    $results = Str::contains($this->resolver->findOrFail('')->getText(), $text);

                    if ($results) {
                        $callback && $callback($this);
                    }

                    return $results;
                }, $message);
            },

            'whenElementAvailable' => function ($selector, $callbackOrSeconds = null, $seconds = null) {
                $callback = null;
                if (is_callable($callbackOrSeconds)) {
                    $callback = $callbackOrSeconds;
                } elseif (is_numeric($callbackOrSeconds)) {
                    $seconds = $callbackOrSeconds;
                }

                return $this->whenAvailable($selector, function ($value) use ($callback) {
                    $callback && $callback($value);
                }, $seconds);
            },

            'hasInput' => function ($field) {
                /* @var \Facebook\WebDriver\Remote\RemoteWebElement $element */
                $this->resolver->resolveForTyping($field);

                return $this;
            },

            'wait' => function ($seconds, \Closure $callback = null) {
                try {
                    $this->waitUsing($seconds, 200, function () {});
                } catch (TimeoutException $e) {
                    $callback && $callback();
                }

                return $this;
            },

            'assertHidden' => function ($selector) {
                $fullSelector = $this->resolver->format($selector);

                PHPUnit::assertTrue(
                    $this->resolver->findOrFail($selector)->isDisplayed(),
                    "Element [{$fullSelector}] is visible."
                );

                return $this;
            },
            'assert' => function (Component $component) {
                return $this->with($component, function () {});
            },
        ];

        foreach ($functions as $method => $callback) {
            Browser::macro($method, $callback);
        }
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
