<?php

namespace Dcat\Admin\Form\Field;

class Icon extends Text
{
    public static $js = '@fontawesome-iconpicker';
    public static $css = '@fontawesome-iconpicker';

    public function render()
    {
        $this->addScript();

        $this->prepend("<i class='fa {$this->value()}'>&nbsp;</i>")
            ->defaultAttribute('autocomplete', 'off')
            ->defaultAttribute('style', 'width: 160px;flex:none');

        return parent::render();
    }

    protected function addScript()
    {
        $this->script = <<<JS
setTimeout(function () {
    var field = $('{$this->getElementClassSelector()}'),
        parent = field.parents('.form-field'),
        showIcon = function (icon) {
            parent.find('.input-group-prepend .input-group-text').html('<i class="' + icon + '"></i>');
        };
    
    field.iconpicker({placement:'bottomLeft', animation: false});
    
    parent.find('.iconpicker-item').on('click', function (e) {
       showIcon($(this).find('i').attr('class'));
    });
    
    field.on('keyup', function (e) {
        var val = $(this).val();
        
        if (val.indexOf('fa-') !== -1) {
            if (val.indexOf('fa ') === -1) {
                val = 'fa ' + val;
            }
        }
        
        showIcon(val);
    })
}, 1);
JS;
    }
}
