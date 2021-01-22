<?php

namespace Dcat\Admin\Traits;

use Dcat\Admin\Support\Helper;
use DOMDocument;
use DOMElement;

trait HasHtml
{
    protected static $shouldResolveTags = ['style', 'script'];

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
     * @param array  $data
     *
     * @return string
     *
     * @throws \Throwable
     */
    public static function view(string $view, array $data = [], array $options = [])
    {
        return static::resolveHtml(view($view, $data), $options)['html'];
    }

    /**
     * @param string|\Illuminate\Contracts\Support\Renderable $content
     * @param array                                           $data
     * @param array                                           $options
     *
     * @throws \Throwable
     *
     * @return array ['html' => $html, 'script' => $script]
     */
    public static function resolveHtml($content, array $options = []): array
    {
        $dom = static::getDOMDocument(Helper::render($content));

        $head = static::resolveElement($dom->getElementsByTagName('head')->item(0) ?: null);
        $body = static::resolveElement($dom->getElementsByTagName('body')->item(0) ?: null);

        $script = $head['script'].$body['script'];

        $runScript = $options['runScript'] ?? true;
        if ($runScript) {
            static::script($script);

            $script = '';
        }

        return ['html' => $head['html'].$body['html'], 'script' => $script];
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
                static::asset()->require(explode(',', $require));
            }

            if ($init = $element->getAttribute('init')) {
                $init = str_replace("'", "\\'", $init);

                $script = "Dcat.init('{$init}', function (\$this, id) { {$script}\n });";
            } else {
                $script = "(function () {{$script}\n})();";
            }

            if ($element->hasAttribute('once') || $element->hasAttribute('first')) {
                return static::script($script, $element->hasAttribute('first'));
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
