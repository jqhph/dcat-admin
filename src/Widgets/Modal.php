<?php

namespace Dcat\Admin\Widgets;

use Closure;
use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Support\LazyRenderable;
use Dcat\Admin\Traits\AsyncRenderable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;

class Modal extends Widget
{
    use AsyncRenderable;

    /**
     * @var string|Closure|Renderable
     */
    protected $title;

    /**
     * @var string|Closure|Renderable
     */
    protected $content;

    /**
     * @var string|Closure|Renderable
     */
    protected $footer;

    /**
     * @var string|Closure|Renderable
     */
    protected $button;

    /**
     * @var string
     */
    protected $size = '';

    /**
     * @var array
     */
    protected $events = [];

    /**
     * @var int
     */
    protected $delay = 10;

    /**
     * @var string
     */
    protected $load = '';

    /**
     * @var bool
     */
    protected $join = false;

    /**
     * Modal constructor.
     *
     * @param string|Closure|Renderable                $title
     * @param string|Closure|Renderable|LazyRenderable $content
     */
    public function __construct($title = null, $content = null)
    {
        $this->id('modal-'.Str::random(8));
        $this->title($title);
        $this->content($content);

        $this->class('modal fade');
    }

    /**
     * 设置弹窗尺寸.
     *
     * @param string $size
     *
     * @return $this
     */
    public function size(string $size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * 设置弹窗尺寸为 sm 300px.
     *
     * @return $this
     */
    public function sm()
    {
        return $this->size('sm');
    }

    /**
     * 设置弹窗尺寸为 lg 800px.
     *
     * @return $this
     */
    public function lg()
    {
        return $this->size('lg');
    }

    /**
     * 设置弹窗尺寸为 xl 1140px.
     *
     * @return $this
     */
    public function xl()
    {
        return $this->size('xl');
    }

    /**
     * 设置loading效果延迟时间.
     *
     * @param int $delay
     *
     * @return $this
     */
    public function delay(int $delay)
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * 设置按钮.
     *
     * @param string|Closure|Renderable $button
     *
     * @return $this
     */
    public function button($button)
    {
        $this->button = $button;

        return $this;
    }

    /**
     * 设置弹窗标题.
     *
     * @param string|Closure|Renderable $title
     *
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * 设置弹窗内容.
     *
     * @param string|Closure|Renderable|LazyRenderable $content
     *
     * @return $this
     */
    public function content($content)
    {
        if ($content instanceof LazyRenderable) {
            $this->setRenderable($content);
        } else {
            $this->content = $content;
        }

        return $this;
    }

    /**
     * @param string|Closure|Renderable|LazyRenderable $content
     *
     * @return $this
     */
    public function body($content)
    {
        return $this->content($content);
    }

    /**
     * 设置是否返回弹窗HTML.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function join(bool $value = true)
    {
        $this->join = $value;

        return $this;
    }

    /**
     * @param string|Closure|Renderable|LazyRenderable $footer
     *
     * @return $this
     */
    public function footer($footer)
    {
        $this->footer = $footer;

        return $this;
    }

    /**
     * 监听弹窗事件.
     *
     * @param string $event
     * @param string $script
     *
     * @return $this
     */
    public function on(string $event, string $script)
    {
        $this->events[] = compact('event', 'script');

        return $this;
    }

    /**
     * 监听弹窗显示事件.
     *
     * @param string $script
     *
     * @return $this
     */
    public function onShow(string $script)
    {
        return $this->on('show.bs.modal', $script);
    }

    /**
     * 监听弹窗已显示事件.
     *
     * @param string $script
     *
     * @return $this
     */
    public function onShown(string $script)
    {
        return $this->on('shown.bs.modal', $script);
    }

    /**
     * 监听弹窗隐藏事件.
     *
     * @param string $script
     *
     * @return $this
     */
    public function onHide(string $script)
    {
        return $this->on('hide.bs.modal', $script);
    }

    /**
     * 监听弹窗已隐藏事件.
     *
     * @param string $script
     *
     * @return $this
     */
    public function onHidden(string $script)
    {
        return $this->on('hidden.bs.modal', $script);
    }

    /**
     * 监听弹窗异步渲染完成事件.
     *
     * @param string $script
     *
     * @return $this
     */
    public function onLoad(string $script)
    {
        $this->load .= "(function () { {$script} })();";

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->getHtmlAttribute('id');
    }

    /**
     * 获取弹窗元素选择器.
     *
     * @return string
     */
    public function getElementSelector()
    {
        return '#'.$this->getId();
    }

    protected function addEventScript()
    {
        if (! $this->events) {
            return;
        }

        $script = '';

        foreach ($this->events as $v) {
            $script .= "modal.on('{$v['event']}', function (event) {
                {$v['script']}
            });";
        }

        $this->script = <<<JS
(function () {
    var modal = $(replaceNestedFormIndex('{$this->getElementSelector()}'));
    {$script}
})();
JS;
    }

    protected function addRenderableScript()
    {
        if (! $url = $this->getRequestUrl()) {
            return;
        }

        $this->on('show.bs.modal', <<<JS
var modal = $(this), body = modal.find('.modal-body');

body.html('<div style="min-height:150px"></div>').loading();
        
setTimeout(function () {
    Dcat.helpers.asyncRender('{$url}', function (html) {
        body.html(html);

        {$this->load}
    });
}, {$this->delay});
JS
        );
    }

    public function render()
    {
        $this->addRenderableScript();
        $this->addEventScript();

        if ($this->join) {
            return $this->renderButton().parent::render();
        }

        Admin::html(parent::render());

        return $this->renderButton();
    }

    public function html()
    {
        return <<<HTML
<div {$this->formatHtmlAttributes()} role="dialog">
    <div class="modal-dialog modal-{$this->size}">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{$this->renderTitle()}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">{$this->renderContent()}</div>
            {$this->renderFooter()}
        </div>
    </div>
</div>
HTML;
    }

    protected function renderTitle()
    {
        return Helper::render($this->title);
    }

    protected function renderContent()
    {
        return Helper::render($this->content);
    }

    protected function renderFooter()
    {
        $footer = Helper::render($this->footer);

        if (! $footer) {
            return;
        }

        return <<<HTML
<div class="modal-footer">{$footer}</div>
HTML;
    }

    protected function renderButton()
    {
        if (! $this->button) {
            return;
        }

        $button = Helper::render($this->button);

        // 如果没有HTML标签则添加一个 a 标签
        if (! preg_match('/(\<\/[\d\w]+\s*\>+)/i', $button)) {
            $button = "<a href=\"javascript:void(0)\">{$button}</a>";
        }

        return <<<HTML
<span style="cursor: pointer" data-toggle="modal" data-target="#{$this->getId()}">{$button}</span>
HTML;
    }
}
