<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Admin;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;

class Markdown extends Widget
{
    /**
     * @var string
     */
    protected $content;

    /**
     * 配置.
     *
     * @var array
     */
    protected $options = [
        'htmlDecode'      => 'style,script,iframe',
        'emoji'           => true,
        'taskList'        => true,
        'tex'             => true,
        'flowChart'       => true,
        'sequenceDiagram' => true,
    ];

    public function __construct($markdown = '')
    {
        $markdown && $this->content($markdown);

        Admin::css([
            'vendor/dcat-admin/editor-md/css/editormd.preview.min.css',
            'vendor/dcat-admin/dcat-admin/markdown.css',
        ]);

        Admin::js([
            'vendor/dcat-admin/editor-md/lib/raphael.min.js',
            'vendor/dcat-admin/editor-md/lib/marked.min.js',
            'vendor/dcat-admin/editor-md/lib/prettify.min.js',
            'vendor/dcat-admin/editor-md/lib/underscore.min.js',
            'vendor/dcat-admin/editor-md/lib/sequence-diagram.min.js',
            'vendor/dcat-admin/editor-md/lib/flowchart.min.js',
            'vendor/dcat-admin/editor-md/lib/jquery.flowchart.min.js',
            'vendor/dcat-admin/editor-md/editormd.min.js',
        ]);
    }

    /**
     * @param mixed $k
     * @param mixed $v
     *
     * @return $this
     */
    public function option($k, $v)
    {
        $this->options[$k] = $v;

        return $this;
    }

    /**
     * @param string|Renderable $markdown
     *
     * @return $this
     */
    public function content($markdown)
    {
        $this->content = &$markdown;

        return $this;
    }

    protected function build()
    {
        if ($this->content instanceof Renderable) {
            $this->content = $this->content->render();
        }

        return <<<EOF
<div {$this->formatHtmlAttributes()}><textarea style="display:none;">{$this->content}</textarea></div>
EOF;
    }

    public function render()
    {
        $id = 'mkd-'.Str::random();

        $this->defaultHtmlAttribute('id', $id);

        $opts = json_encode($this->options);

        Admin::script("editormd.markdownToHTML('$id', $opts);");

        return $this->build();
    }
}
