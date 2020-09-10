<?php

namespace Dcat\Admin\Traits;

use Dcat\Admin\Support\Helper;
use DOMElement;
use DOMDocument;

trait Renderable
{
    private static $shouldResolveTags = ['style', 'script', 'template'];

    /**
     * @var array
     */
    public static $html = [];

    /**
     * @param string $html
     *
     * @return null|string
     */
    public static function html($html = '')
    {
        $html = static::context()->html;

        if (! empty($html)) {

            static::$html = array_merge(
                static::$html,
                array_map([Helper::class, 'render'], (array) $html)
            );

            return;
        }

        return implode('', array_unique(static::$html));
    }

    /**
     * @param string $view
     * @param array  $data
     *
     * @return string
     *
     * @throws \Throwable
     */
    public static function view(string $view, array $data = [])
    {
        return static::render(view($view, $data));
    }

    /**
     * @param string|\Illuminate\Contracts\Support\Renderable $view
     * @param array                                           $data
     *
     * @throws \Throwable
     *
     * @return string
     */
    public static function render($value): string
    {
        $dom = static::getDOMDocument(Helper::render($value));

        $head = $dom->getElementsByTagName('head')->item(0) ?: null;
        $body = $dom->getElementsByTagName('body')->item(0) ?: null;

        return static::resolveElement($head).static::resolveElement($body);
    }

    /**
     * @param string $html
     *
     * @throws \Throwable
     *
     * @return DOMDocument
     */
    protected static function getDOMDocument(string $html)
    {
        $dom = new DOMDocument();

        libxml_use_internal_errors(true);

        $dom->loadHTML('<?xml encoding="utf-8" ?>'.$html);

        libxml_use_internal_errors(false);

        return $dom;
    }

    /**
     * @param DOMElement $element
     *
     * @return void
     */
    protected static function resolve(DOMElement $element)
    {
        $method = 'resolve'.ucfirst($element->tagName);

        return static::{$method}($element);
    }

    /**
     * @param DOMElement $element
     *
     * @return void
     */
    protected static function resolveScript(DOMElement $element)
    {
        if ($element->hasAttribute('src')) {
            static::js($element->getAttribute('src'));

            return;
        }

        if (! empty($script = trim($element->nodeValue))) {
            if ($require = $element->getAttribute('require')) {
                static::asset()->collect($require);
            }

            static::script('(function () {'.$script.'})()');
        }
    }

    /**
     * @param DOMElement $element
     *
     * @return void
     */
    protected static function resolveStyle(DOMElement $element)
    {
        if (! empty(trim($element->nodeValue))) {
            static::style($element->nodeValue);
        }
    }

    /**
     * @param DOMElement $element
     *
     * @return void
     */
    protected static function resolveTemplate(DOMElement $element)
    {
        $html = '';
        foreach ($element->childNodes as $childNode) {
            $html .= $element->ownerDocument->saveHTML($childNode);
        }

        $html && static::html($html);
    }

    protected static function resolveElement(?DOMElement $element)
    {
        $html = '';

        if (! $element) {
            return $html;
        }

        foreach ($element->childNodes as $child) {
            if (
                $element instanceof DOMElement
                && in_array($element->tagName, static::$shouldResolveTags, true)
            ) {
                static::resolve($child);

                continue;
            }

            $html .= trim($element->ownerDocument->saveHTML($child));

            return $html;
        }
    }
}
