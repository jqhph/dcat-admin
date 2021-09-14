<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;

class Markdown extends Widget
{
    protected $view = 'admin::widgets.markdown';

    /**
     * @var string|Renderable
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

    public function __construct($markdown = null)
    {
        if ($markdown !== null) {
            $this->content($markdown);
        }

        $this->id('mkd-'.Str::random(8));
    }

    /**
     * @param  string|Renderable  $markdown
     * @return $this
     */
    public function content($markdown)
    {
        $this->content = &$markdown;

        return $this;
    }

    protected function renderContent()
    {
        return Helper::render($this->content);
    }

    public function render()
    {
        $this->addVariables([
            'id'      => $this->id(),
            'content' => $this->renderContent(),
        ]);

        return parent::render();
    }
}
