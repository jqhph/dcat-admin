<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;

class SwitchDisplay extends AbstractDisplayer
{
    public static $js = '@switchery';
    public static $css = '@switchery';

    protected $selector = 'grid-column-switch';

    /**
     * @var string
     */
    protected $color;

    public function color($color)
    {
        $this->color = Admin::color()->get($color);
    }

    public function display(string $color = '')
    {
        if ($color instanceof \Closure) {
            $color->call($this->row, $this);
        } else {
            $this->color($color);
        }

        $this->addScript();

        $checked = $this->value ? 'checked' : '';
        $color = $this->color ?: Admin::color()->primary();

        return <<<EOF
<input class="{$this->selector}" data-url="{$this->url()}" data-size="small" name="{$this->column->getName()}" {$checked} type="checkbox" data-color="{$color}"/>
EOF;
    }

    protected function url()
    {
        return $this->resource().'/'.$this->getKey();
    }

    protected function addScript()
    {
        Admin::script(
            <<<JS
(function(){
    var swt = $('.{$this->selector}'), that;
    function init(){
        swt.parent().find('.switchery').remove();
        swt.each(function(k){
            that = $(this);
            new Switchery(that[0], that.data())
        })
    } 
    init();
    swt.off('change').change(function(e) {
        var that = $(this), 
         url = that.data('url'),
         checked = that.is(':checked'),
         name = that.attr('name'), 
         data = {
            _token: Dcat.token,
            _method: 'PUT'
         },
         value = checked ? 1 : 0;
        
        if (name.indexOf('.') === -1) {
            data[name] = value;
        } else {
            name = name.split('.');
            
            data[name[0]] = {};
            data[name[0]][name[1]] = value;
        }
        
        Dcat.NP.start();
    
        $.ajax({
            url: url,
            type: "POST",
            data: data,
            success: function (d) {
                Dcat.NP.done();
                if (d.status) {
                    Dcat.success(d.message);
                } else {
                    Dcat.error(d.message);
                }
            }
        });
    });
})();
JS
        );
    }
}
