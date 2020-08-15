<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Admin;
use Dcat\Admin\Grid\LazyRenderable;
use Dcat\Admin\Traits\AsyncRenderable;
use Illuminate\Support\Str;

class AsyncTable extends Widget
{
    use AsyncRenderable;

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
(function () {
    function load(url, box) {
        var $this = $(this);
        
        box = box || $this;
        
        url = $this.data('url') || url;
        if (! url) {
            return;
        }
        
        box.loading({background: 'transparent!important'});
        
        Dcat.helpers.asyncRender(url, function (html) {
            box.loading(false);
            box.html(html);
            bind(box);
            box.trigger('table:loaded');
        });
    }            
                
    function bind(box) {
        function loadLink() {
            load($(this).attr('href'), box);
            
            return false;
        }
        
        box.find('.pagination .page-link').on('click', loadLink);
        box.find('.grid-column-header a').on('click', loadLink);
  
        box.find('form').on('submit', function () {
            load($(this).attr('action')+'&'+$(this).serialize(), box);
            
            return false;
        });
         
        box.find('.filter-box .reset').on('click', loadLink);
    }
    
    $('.table-card').on('table:load', load);
})();
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
