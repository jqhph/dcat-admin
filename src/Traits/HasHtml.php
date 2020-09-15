<?php

namespace Dcat\Admin\Traits;

use Dcat\Admin\Support\Helper;
use DOMElement;
use DOMDocument;

trait HasHtml
{
    protected static $shouldResolveTags = ['style', 'script', 'template'];

    /**
     * @param string|array $content
     *
     * @return null|string
     */
    public static function html($content = null)
    {
        $html = static::context()->html ?: [];

        if ($content === null) {
            return implode('', array_unique($html));
        }

        static::context()->html = array_merge(
            $html,
            array_map([Helper::class, 'render'], (array) $content)
        );
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
        return static::resolveHtml(view($view, $data))[0];
    }

    /**
     * @param string|\Illuminate\Contracts\Support\Renderable $content
     * @param array                                           $data
     * @param array                                           $options
     *
     * @throws \Throwable
     *
     * @return array [$html, $script]
     */
    public static function resolveHtml($content, array $options = []): array
    {
        $dom = static::getDOMDocument(Helper::render($content));

        $head = $dom->getElementsByTagName('head')->item(0) ?: null;
        $body = $dom->getElementsByTagName('body')->item(0) ?: null;

        $head = static::resolveElement($head);
        $body = static::resolveElement($body);

        $script = $head['script'].$body['script'];

        $runScript = $options['runScript'] ?? true;
        if ($runScript) {
            static::script($script);

            $script = '';
        }

        return [$head['html'].$body['html'], $script];
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
     * @return void|string
     */
    protected static function resolve(DOMElement $element)
    {
        $method = 'resolve'.ucfirst($element->tagName);

        return static::{$method}($element);
    }

    /**
     * @param DOMElement $element
     *
     * @return string|void
     */
    protected static function resolveScript(DOMElement $element)
    {
        if ($element->hasAttribute('src')) {
            static::js($element->getAttribute('src'));

            return;
        }

        if (! empty($script = trim($element->nodeValue))) {
            if ($require = $element->getAttribute('require')) {
                static::asset()->collect(explode(',', $require));
            }

            $script = '(function () {'.$script.'})();';

            if ($element->hasAttribute('once')) {
                return static::script($script);
            }

            return $script;
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
        $html = $script = '';

        if (! $element) {
            return ['html' => $html, 'script' => $script];
        }

        foreach ($element->childNodes as $child) {
            if (
                $child instanceof DOMElement
                && in_array($child->tagName, static::$shouldResolveTags, true)
            ) {
                $script .= static::resolve($child);

                continue;
            }

            $html .= trim($element->ownerDocument->saveHTML($child));
        }

        return ['html' => $html, 'script' => $script];
    }
}
