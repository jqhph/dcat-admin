<?php

namespace Dcat\Admin\Widgets;

use Illuminate\Contracts\Support\Renderable;

class Dump extends Widget
{
    /**
     * @var string
     */
    protected $view = 'admin::widgets.dump';

    /**
     * @var string
     */
    protected $padding = '10px';

    /**
     * @var string
     */
    protected $content = '';

    /**
     * Dump constructor.
     *
     * @param array|object|string $content
     * @param string|null $padding
     */
    public function __construct($content, string $padding = null)
    {
        $content = $this->getJson($content) ?: $content;

        if ($content instanceof Renderable) {
            $this->content = $content->render();
        } elseif (is_array($content) || is_object($content)) {
            $this->content = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            $this->content = $content;
        }
        if ($padding) {
            $this->padding = $padding;
        }
    }

    public function padding(?string $padding)
    {
        $this->padding = $padding;

        return $this;
    }

    /**
     * @param mixed $content
     * @return bool
     */
    protected function getJson($content)
    {
        if (!is_string($content)) {
            return false;
        }
        return json_decode($content);
    }

    public function render()
    {
        $this->defaultAttribute('style', 'white-space:pre-wrap');

        return <<<EOF
<div style="padding:{$this->padding}"><pre class="dump" {$this->formatAttributes()}>{$this->content}</pre></div>
EOF;

    }
}
