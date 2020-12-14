<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Grid\LazyRenderable;
use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Renderable;

class DialogTable extends Widget
{
    protected $view = 'admin::widgets.dialogtable';

    /**
     * @var string
     */
    protected $title;

    /**
     * @var LazyTable
     */
    protected $table;

    /**
     * @var string
     */
    protected $width = '825px';

    /**
     * @var string|\Closure|Renderable
     */
    protected $button;

    /**
     * @var string|\Closure|Renderable
     */
    protected $footer;

    /**
     * @var array
     */
    protected $events = ['shown' => null, 'hidden' => null, 'load' => null];

    public function __construct($title = null, LazyRenderable $table = null)
    {
        if ($title instanceof LazyRenderable) {
            $table = $title;
            $title = null;
        }

        $this->title($title);
        $this->from($table);

        $this->elementClass = 'dialog-table-container';

        $this->class('dialog-table');
    }

    /**
     * 设置异步表格实例.
     *
     * @param LazyRenderable|null $renderable
     *
     * @return $this
     */
    public function from(?LazyRenderable $renderable)
    {
        if (! $renderable) {
            return $this;
        }

        $this->table = LazyTable::make($renderable)->simple()->runScript(false);

        return $this;
    }

    /**
     * 设置弹窗标题.
     *
     * @param string $title
     *
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * 设置弹窗宽度.
     *
     * @example
     *    $this->width('500px');
     *    $this->width('50%');
     *
     * @param string $width
     *
     * @return $this
     */
    public function width($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * 设置点击按钮HTML.
     *
     * @param string|\Closure|Renderable $button
     *
     * @return $this
     */
    public function button($button)
    {
        $this->button = $button;

        return $this;
    }

    /**
     * 监听弹窗打开事件.
     *
     * @param string $script
     *
     * @return $this
     */
    public function onShown(string $script)
    {
        $this->events['shown'] .= ';'.$script;

        return $this;
    }

    /**
     * 监听弹窗隐藏事件.
     *
     * @param string $script
     *
     * @return $this
     */
    public function onHidden(string $script)
    {
        $this->events['hidden'] .= ';'.$script;

        return $this;
    }

    /**
     * 监听表格加载完毕事件.
     *
     * @param string $script
     *
     * @return $this
     */
    public function onLoad(string $script)
    {
        $this->events['load'] .= ';'.$script;

        return $this;
    }

    /**
     * 设置弹窗底部内容.
     *
     * @param string|\Closure|Renderable $footer
     *
     * @return $this
     */
    public function footer($footer)
    {
        $this->footer = $footer;

        return $this;
    }

    /**
     * @return LazyTable
     */
    public function getTable()
    {
        return $this->table;
    }

    public function render()
    {
        $this->addVariables([
            'title'  => $this->title,
            'width'  => $this->width,
            'button' => $this->renderButton(),
            'table'  => $this->renderTable(),
            'footer' => $this->renderFooter(),
            'events' => $this->events,
        ]);

        return parent::render();
    }

    protected function renderTable()
    {
        return $this->table->render();
    }

    protected function renderFooter()
    {
        return Helper::render($this->footer);
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

        return $button;
    }
}
