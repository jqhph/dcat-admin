<?php

namespace Tests;

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
            // 等待文本可见
            'whenTextAvailable' => function ($text, $callbackOrSeconds = null, $seconds = null) {
                $callback = null;

                if (is_callable($callbackOrSeconds)) {
                    $callback = $callbackOrSeconds;
                } elseif (is_numeric($callbackOrSeconds)) {
                    $seconds = $callbackOrSeconds;
                }

                $text = Arr::wrap($text);
                $message = $this->formatTimeOutMessage('Waited %s seconds for text', implode("', '", $text));

                return $this->waitUsing($seconds, 100, function () use ($text, $callback) {
                    $results = Str::contains($this->resolver->findOrFail('')->getText(), $text);

                    if ($results) {
                        $callback && $callback($this);
                    }

                    return $results;
                }, $message);
            },
            // 等待元素可见
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
            // 判断input框是否存在
            'hasInput' => function ($field) {
                /* @var \Facebook\WebDriver\Remote\RemoteWebElement $element */
                $this->resolver->resolveForTyping($field);

                return $this;
            },
            // 判断元素是否不可见
            'assertHidden' => function ($selector) {
                $fullSelector = $this->resolver->format($selector);

                $isHidden = $this->script(
                    <<<JS
var display = $('{$fullSelector}').css('display');

return display === 'none' || $('{$fullSelector}').is(':hidden');
JS
                );

                PHPUnit::assertTrue(
                    (bool) ($isHidden[0] ?? false),
                    "Element [{$fullSelector}] is displayed."
                );

                return $this;
            },
            // 判断是否是给定组件
            'is' => function (Component $component) {
                return $this->with($component, function () {
                });
            },
            // 判断文本是否存在，忽略大小写
            'assertSeeTextIn' => function (?string $selector, ?string $text) {
                $fullSelector = $this->resolver->format($selector);

                $element = $this->resolver->findOrFail($selector);

                PHPUnit::assertTrue(
                    Str::contains(strtolower($element->getText()), strtolower($text)),
                    "Did not see expected text [{$text}] within element [{$fullSelector}]."
                );

                return $this;
            },
            // 判断文本是否存在，忽略大小写
            'assertSeeText' => function (?string $text) {
                return $this->assertSeeTextIn('', $text);
            },
            // 判断全页面中是否存在文本
            'assertSeeInBody' => function (?string $text) {
                $resolver = clone $this->resolver;
                $resolver->prefix = 'html';

                $element = $resolver->findOrFail('');

                PHPUnit::assertTrue(
                    Str::contains(strtolower($element->getText()), strtolower($text)),
                    "Did not see expected text [{$text}] within element [html]."
                );

                return $this;
            },
            // 等待全页面出现文本
            'waitForTextInBody' => function ($text, $seconds = null) {
                $text = Arr::wrap($text);

                $message = $this->formatTimeOutMessage('Waited %s seconds for text', implode("', '", $text));

                $resolver = clone $this->resolver;
                $resolver->prefix = 'html';

                return $this->waitUsing($seconds, 100, function () use ($resolver, $text) {
                    return Str::contains($resolver->findOrFail('')->getText(), $text);
                }, $message);
            },
            // 滚动到页面底部
            'scrollToBottom' => function () {
                $this->script(
                    <<<'JS'
$(document).scrollTop($(document).height() - $(window).height());
JS
                );

                return $this;
            },
            'scrollToTop' => function () {
                $this->script(
                    <<<'JS'
$(document).scrollTop(0);
JS
                );

                return $this;
            },
        ];

        foreach ($functions as $method => $callback) {
            Browser::macro($method, $callback);
        }
    }

    public function makeDelayBrowser($browser)
    {
        return new class($browser)
        {
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
