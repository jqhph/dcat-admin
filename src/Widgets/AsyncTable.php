<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid\LazyRenderable;
use Dcat\Admin\Traits\AsyncRenderable;
use Illuminate\Support\Str;

class AsyncTable extends Widget
{
    use AsyncRenderable;

    public static $js = [
        '@grid-extension',
    ];

    protected $load = true;

    public function __construct(LazyRenderable $renderable = null, bool $load = true)
    {
        $this->setRenderable($renderable);
        $this->load($load);

        $this->id('table-card-'.Str::random(8));
        $this->class('table-card');
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
        Admin::script(<<<'JS'
Dcat.grid.AsyncTable('.table-card');
JS
        );

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
$(replaceNestedFormIndex('{$this->getElementSelector()}')).trigger('table:load');
JS;
    }

    public function render()
    {
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
