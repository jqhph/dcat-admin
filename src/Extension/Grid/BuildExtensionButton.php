<?php

namespace Dcat\Admin\Extension\Grid;

use Dcat\Admin\Admin;
use Illuminate\Contracts\Support\Renderable;

class BuildExtensionButton implements Renderable
{
    public function render()
    {
        Admin::style(
            <<<'CSS'
.popover {max-width:350px}
CSS
        );

        $this->setupScript();

        $label = trans('admin.new');

        return "<a id='create-extension' class='btn btn-success btn-sm'><i class=\"ti-plus\"></i> &nbsp;$label</a>";
    }

    protected function setupScript()
    {
        $submit = trans('admin.submit');

        $url = admin_url('helpers/extensions/create');

        Admin::script(
            <<<JS
            
$('#create-extension').popover({
    html: true,
    title: false,
    content: function () {
        return '<div class="form-group " style="margin-top:5px"><error></error><div class="input-group input-group-sm"><span class="input-group-addon"><i class="ti-pencil"></i></span><input type="text" class="form-control " placeholder="Package Name" name="name" ></div></div>'
        + '<div class="form-group"><error></error><div class="input-group input-group-sm"><span class="input-group-addon"><i class="ti-pencil"></i></span><input type="text" class="form-control " placeholder="Namespace" name="namespace" value="Dcat\\\\Admin\\\\Extension\\\\Your name" ></div></div>'
        + '<button id="submit-create" class="btn btn-primary btn-sm waves-effect waves-light">{$submit}</button>'
        
    }
});

$('#create-extension').on('shown.bs.popover', function () {
    var errTpl = '<label class="control-label"><i class="fa fa-times-circle-o"></i> {msg}</label>';
    $('#submit-create').click(function () {
        var _name = $('input[name="name"]'),
            _namespace = $('input[name="namespace"]'),
            name = _name.val(), 
            namespace = _namespace.val();
        
        if (!name) {
            return displayError(_name, 'The Name is required.');
        }
        if (!isValid(name) || name.indexOf('/') === -1) {
            return displayError(_name, 'The "'+name+'" is not a valid package name, please input a name like ":vendor/:name".');
        }
        removeError(_name);
        
        if (!namespace) {
            return displayError(_namespace, 'The Namespace is required.');
        }
         if (!isValid(namespace)) {
            return displayError(_namespace, 'The "'+namespace+'" is not a valid namespace.');
        }
        removeError(_namespace);
        
        $('.popover').loading();
        $.post('$url', {
            _token: LA.token,
            name: name,
            namespace: namespace,
        }, function (response) {
            $('.popover').loading(false);
        
           if (!response.status) {
               LA.error(response.message);
           } else {
               $('#create-extension').popover('hide');
           }
           
           $('.content').prepend('<div class="row"><div class="col-md-12">'+response.content+'</div></div>');
        });
        
    });
    
    function displayError(obj, msg) {
        obj.parents('.form-group').addClass('has-error');
        obj.parents('.form-group').find('error').html(errTpl.replace('{msg}', msg));
    }
    
    function removeError(obj) {
        obj.parents('.form-group').removeClass('has-error');
        obj.parents('.form-group').find('error').html('');
    }
    
    function isValid(str) { 
        return /^[\w-\/\\\\]+$/.test(str); 
    }
    
});

JS
        );
    }
}
