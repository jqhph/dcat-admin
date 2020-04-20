<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;

class SwitchDisplay extends AbstractDisplayer
{
    public static $js = '@switchery';
    public static $css = '@switchery';

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

        $this->setupScript();

        $name = $this->getElementName();
        $key = $this->getKey();
        $checked = $this->value ? 'checked' : '';
        $color = $this->color ?: Admin::color()->primary();

        return <<<EOF
<input class="grid-switch-{$this->grid->getName()}" data-size="small" name="{$name}" data-key="$key" {$checked} type="checkbox" data-color="{$color}"/>
EOF;
    }

    protected function setupScript()
    {
        Admin::script(
            <<<JS
(function(){
    var swt = $('.grid-switch-{$this->grid->getName()}'), t;
    function init(){
        swt.parent().find('.switchery').remove();
        swt.each(function(k){
            t = $(this);
            new Switchery(t[0], t.data())
        })
    } 
    init();
    swt.off('change').change(function(e) {
        var t = $(this), id = t.data('key'), checked = t.is(':checked'), name = t.attr('name'), data = {
            _token: Dcat.token,
            _method: 'PUT'
        };
        data[name] = checked ? 1 : 0;
        Dcat.NP.start();
    
        $.ajax({
            url: "{$this->resource()}/" + id,
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
