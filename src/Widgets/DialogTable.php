<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Grid\LazyRenderable;
use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;

class DialogTable extends Widget
{
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
    protected $width = '800px';

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

        $this->class('dialog-table');
        $this->id('dialog-table-'.Str::random(8));
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

    protected function addScript()
    {
        if ($this->events['load']) {
            $this->table->onLoad($this->events['load']);
        }

        $this->script = <<<JS
(function () {
    var id = replaceNestedFormIndex('{$this->id()}'), 
        _id, _tempId, _btnId, _tb;
    
    setId(id);
    
    function hidden(index) {
        {$this->events['hidden']}
        
        $(_id).trigger('dialog:hidden');
    }
    
    function open(btn) {
        var index = layer.open({
          type: 1,
          area: '{$this->width}',
          offset: '70px',
          maxmin: false,
          resize: false,
          content: $(_tempId).html(),
          success: function(layero, index) {
              $(_id).attr('layer', index);
              
              setDataId($(_id));
              
              {$this->events['shown']}
              
              setTimeout(function () {
                  Dcat.grid.AsyncTable({container: _tb});
                  
                  $(_tb).trigger('table:load');
              }, 100);
              
              $(_id).trigger('dialog:shown');
              
              $(_id).on('dialog:open', openDialog);
              $(_id).on('dialog:close', closeDialog)
          },
          cancel: function (index, layero) {
              btn && btn.removeAttr('layer');
              
              hidden(index)
          }
        });
        
        btn && btn.attr('layer', index);
    }
    
    function setDataId(obj) {
        if (! obj.attr('data-id')) {
            obj.attr('data-id', id);
        }
    }
    
    function setId(val) {
        if (! val) return;
        
        id = val;
        _id = '#'+id;
        _tempId = '#temp-'+id;
        _btnId = '#button-'+id;
        _tb = _id+' .async-table';
    }
    
    function openDialog () {
        setId($(this).attr('data-id'));
        setDataId($(this));
        
        if (! $(this).attr('layer')) {
            open($(this));
        }
    }
    
    function closeDialog() {
        var index = $(this).attr('layer');
        
        $(_id).removeAttr('layer');
        $(_btnId).removeAttr('layer');
        
        if (index) {
            layer.close(index);
            hidden(index);
        }
    }
    
    $(_btnId).on('click', openDialog);
})();
JS;
    }

    public function html()
    {
        $table = $this->renderTable();

        $this->addScript();

        return <<<HTML
{$this->renderButton()}
<template id="temp-{$this->id()}">
    <div {$this->formatHtmlAttributes()}>
        <div class="p-2 dialog-body">{$table}</div>
        {$this->renderFooter()}
    </div>
</template>
HTML;
    }

    protected function renderTable()
    {
        return <<<HMLT
{$this->table->render()}
HMLT;
    }

    protected function renderFooter()
    {
        $footer = Helper::render($this->footer);

        if (! $footer) {
            return;
        }

        return <<<HTML
<div class="dialog-footer layui-layer-btn">{$footer}</div>
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
<span style="cursor: pointer" id="button-{$this->id()}">{$button}</span>
HTML;
    }
}
