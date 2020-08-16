<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Grid\LazyRenderable;
use Dcat\Admin\Traits\AsyncRenderable;
use Illuminate\Support\Str;

class AsyncTable extends Widget
{
    use AsyncRenderable;

    public static $js = [
        '@grid-extension',
    ];

    /**
     * 设置是否自动加载.
     *
     * @var bool
     */
    protected $load = true;

    /**
     * 设置是否启用表格简化模式.
     *
     * @var bool
     */
    protected $simple;

    /**
     * AsyncTable constructor.
     *
     * @param LazyRenderable $renderable
     * @param bool $load
     */
    public function __construct(LazyRenderable $renderable, bool $load = true)
    {
        $this->setRenderable($renderable);
        $this->load($load);

        $this->id('table-card-'.Str::random(8));
    }

    /**
     * 设置是否自动加载.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function load(bool $value)
    {
        $this->load = $value;

        return $this;
    }

    /**
     * 设置是否启用表格简化模式.
     *
     * @param bool $value
     *
     * @return $this
     */
    public function simple(bool $value = true)
    {
        $this->simple = $value;

        if ($value) {
            $this->class('table-card', true);
        }

        return $this;
    }

    /**
     * 监听异步渲染完成事件.
     *
     * @param string $script
     *
     * @return $this
     */
    public function onLoad(string $script)
    {
        $this->script .= "$(replaceNestedFormIndex('{$this->getElementSelector()}')).on('table:loaded', function (event) { {$script} });";

        return $this;
    }

    protected function addScript()
    {
        $this->script = <<<JS
Dcat.grid.AsyncTable({container: replaceNestedFormIndex('{$this->getElementSelector()}')});
JS;

        if ($this->load) {
            $this->script .= $this->getLoadScript();
        }
    }

    /**
     * @return string
     */
    public function getElementSelector()
    {
        return '#'.$this->getHtmlAttribute('id');
    }

    /**
     * @return string
     */
    public function getLoadScript()
    {
        return <<<JS
$('{$this->getElementSelector()}').trigger('table:load');
JS;
    }

    public function render()
    {
        if ($this->simple !== null) {
            $this->renderable->simple();
        }

        $this->addScript();

        return parent::render();
    }

    public function html()
    {
        $this->setHtmlAttribute([
            'data-url' => $this->getRequestUrl(),
        ]);

        return <<<HTML
<div {$this->formatHtmlAttributes()} style="min-height: 200px"></div>        
HTML;
    }
}
