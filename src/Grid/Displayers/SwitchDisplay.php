<?php

namespace Dcat\Admin\Grid\Displayers;

use Dcat\Admin\Admin;
use Dcat\Admin\Widgets\Color;

class SwitchDisplay extends AbstractDisplayer
{
    /**
     * @var string
     */
    protected $color;

    public function green()
    {
        $this->color = Color::success();
    }

    public function custom()
    {
        $this->color = Color::custom();
    }

    public function yellow()
    {
        $this->color = Color::warning();
    }

    public function red()
    {
        $this->color = Color::danger();
    }

    public function purple()
    {
        $this->color = Color::purple();
    }

    public function blue()
    {
        $this->color = Color::blue();
    }

    /**
     * Set color of the switcher.
     *
     * @param string $color
     *
     * @return $this
     */
    public function color($color)
    {
        $this->color = $color;
    }

    public function display(string $color = '')
    {
        if ($color instanceof \Closure) {
            $color->call($this->row, $this);
        } else {
            if ($color) {
                if (method_exists($this, $color)) {
                    $this->$color();
                } else {
                    $this->color($color);
                }
            }
        }

        $this->setupScript();

        $name = $this->elementName();
        $key = $this->row->{$this->grid->keyName()};
        $checked = $this->value ? 'checked' : '';
        $color = $this->color ?: Color::primary();

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
        swt.each(function(k){
            t = $(this);
            new Switchery(t[0], t.data())
        })
    } 
    init();
    swt.change(function(e) {
        var t = $(this), id = t.data('key'), checked = t.is(':checked'), name = t.attr('name'), data = {
            _token: LA.token,
            _method: 'PUT'
        };
        data[name] = checked ? 1 : 0;
        LA.NP.start();
    
        $.ajax({
            url: "{$this->resource()}/" + id,
            type: "POST",
            data: data,
            success: function (d) {
                LA.NP.done();
                if (d.status) {
                    LA.success(d.message);
                } else {
                    LA.error(d.message);
                }
            }
        });
    });
})();
JS
        );
    }

    protected function collectAssets()
    {
        Admin::collectComponentAssets('switchery');
    }
}
