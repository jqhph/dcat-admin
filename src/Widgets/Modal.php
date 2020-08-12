<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Traits\AsyncRenderable;
use Dcat\Admin\Support\LazyRenderable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;
use Closure;

class Modal extends Widget
{
    use AsyncRenderable;

    protected $view = 'admin::widgets.modal';

    /**
     * @var string
     */
    protected $id;

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
    }

    /**
     * 设置弹窗ID.
     *
     * @param string $id
     *
     * @return $this
     */
    public function id(string $id)
    {
        $this->id = $id;

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
     * 设置弹窗尺寸为 sm 300px
     *
     * @return $this
     */
    public function sm()
    {
        return $this->size('sm');
    }

    /**
     * 设置弹窗尺寸为 lg 800px
     *
     * @return $this
     */
    public function lg()
    {
        return $this->size('lg');
    }

    /**
     * 设置弹窗尺寸为 xl 1140px
     *
     * @return $this
     */
    public function xl()
    {
        return $this->size('xl');
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
     * @param $content
     *
     * @return $this
     */
    public function body($content)
    {
        return $this->content($content);
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
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * 获取弹窗元素选择器
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
    var modal = $('{$this->getElementSelector()}');
    {$script}
})();
JS;
    }

    protected function addRenderableScript()
    {
        $script = $this->getRenderableScript();
        if (! $script) {
            return;
        }

        $this->on('show.bs.modal', <<<JS
var modal = $(this).find('.modal-body');

modal.html('<div style="min-height:150px"></div>').loading();
        
{$script}

render(function (html) {
    modal.html(html);
});
JS
);
    }

    public function render()
    {
        $this->addRenderableScript();
        $this->addEventScript();

        $this->with([
            'id'      => $this->id,
            'size'    => $this->size,
            'title'   => $this->renderTitle(),
            'content' => $this->renderContent(),
        ]);

        Admin::html(parent::render());

        return $this->renderButton();
    }

    protected function renderTitle()
    {
        return Helper::render($this->title);
    }

    protected function renderContent()
    {
        return Helper::render($this->content);
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
<span class="grid-expand" data-toggle="modal" data-target="#{$this->getId()}">
   $button
</span>
HTML;
    }
}
