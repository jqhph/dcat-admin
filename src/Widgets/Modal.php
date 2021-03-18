<?php

namespace Dcat\Admin\Widgets;

use Closure;
use Dcat\Admin\Admin;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Grid\LazyRenderable as LazyGrid;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\InteractsWithRenderApi;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;

class Modal extends Widget
{
    use InteractsWithRenderApi;

    protected $target = 'modal';

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
     * @var string
     */
    protected $centered = '';

    /**
     * @var string
     */
    protected $scrollable = '';
    /**
     * @var array
     */
    protected $events = [];

    /**
     * @var int
     */
    protected $delay = 10;

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
        $this->id('modal-'.Str::random(10));
        $this->title($title);
        $this->content($content);

        $this->class('modal fade');
    }

    /**
     * 设置弹窗垂直居中.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function centered(bool $value = true)
    {
        $this->centered = $value ? 'modal-dialog-centered' : '';

        return $this;
    }

    /**
     * 设置弹窗内容滚动.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function scrollable(bool $value = true)
    {
        $this->scrollable = $value ? 'modal-dialog-scrollable' : '';

        return $this;
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
        if ($content instanceof LazyGrid) {
            $content = $table =
                LazyTable::make()
                ->from($content)
                ->simple()
                ->load(false);

            $this->onShow("target.find('{$table->getElementSelector()}').trigger('table:load')");
        }

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
     * 设置弹窗底部内容.
     *
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

    protected function addScript()
    {
        if (! $this->events) {
            return;
        }

        $script = '';

        foreach ($this->events as $v) {
            $script .= "target.on('{$v['event']}', function (event) {
                {$v['script']}
            });";
        }

        $this->script = <<<JS
(function () {
    var target = $('#{$this->id()}'), body = target.find('.modal-body');
    {$this->getRenderableScript()}
    {$script}
})();
JS;
    }

    protected function addLoadRenderableScript()
    {
        if (! $this->getRenderable()) {
            return;
        }

        $this->on('show.bs.modal', <<<JS
body.html('<div style="min-height:150px"></div>').loading();
        
setTimeout(function () {
    target.trigger('{$this->target}:load')
}, {$this->delay});
JS
        );
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $this->addLoadRenderableScript();
        $this->addScript();

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
    <div class="modal-dialog {$this->centered} {$this->scrollable} modal-{$this->size}">
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
<span style="cursor: pointer" data-toggle="modal" data-target="#{$this->id()}">{$button}</span>
HTML;
    }
}
