<?php

namespace Dcat\Admin\Widgets;

use Illuminate\Contracts\Support\Renderable;

class Dump extends Widget
{
    /**
     * @var string
     */
    protected $padding = '10px';

    /**
     * @var string
     */
    protected $content = '';

    protected $maxWidth;

    /**
     * Dump constructor.
     *
     * @param  array|object|string  $content
     * @param  string|null  $padding
     */
    public function __construct($content, string $padding = null)
    {
        $this->content($content);
        $this->padding($padding);
    }

    public function content($content)
    {
        $content = $this->convertJsonToArray($content) ?: $content;

        if ($content instanceof Renderable) {
            $this->content = $content->render();
        } elseif (is_array($content) || is_object($content)) {
            $this->content = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            $this->content = $content;
        }

        return $this;
    }

    /**
     * @param  string|null  $padding
     * @return $this
     */
    public function padding(?string $padding)
    {
        if ($padding) {
            $this->padding = $padding;
        }

        return $this;
    }

    /**
     * @param  string  $width
     * @return $this
     */
    public function maxWidth($width)
    {
        $this->maxWidth = $width;

        return $this;
    }

    /**
     * @param  mixed  $content
     * @return array|null
     */
    protected function convertJsonToArray($content)
    {
        if (
            is_string($content) &&
            (
                (mb_strpos($content, '{') === 0 && mb_strpos($content, '}', -1) !== false) ||
                (mb_strpos($content, '[') === 0 && mb_strpos($content, ']', -1) !== false)
            )
        ) {
            return json_decode($content, true);
        }
    }

    public function render()
    {
        $this->defaultHtmlAttribute(
            'style',
            'white-space:pre-wrap;'.($this->maxWidth ? "max-width:{$this->maxWidth};" : '')
        );

        return <<<EOF
<div style="padding:{$this->padding}"><pre class="dump" {$this->formatHtmlAttributes()}>{$this->content}</pre></div>
EOF;
    }
}
