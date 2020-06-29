<?php


namespace Dcat\Admin\Widgets;


use Dcat\Admin\Admin;
use Illuminate\Contracts\Support\Renderable;

class LockScreenView implements Renderable
{

    public function render()
    {
        $this->addScript();
        $this->addStyle();

        return view('admin::widgets.lock')->toHtml();
    }

    protected function addStyle()
    {
        Admin::style('.mock{
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.99);
        z-index: 999999;
    }
    .form-mock{
        position: absolute;
        width: 300px;
        height: 80px;
        line-height: 80px;
        margin-top: -40px;
        margin-left: -150px;
        left: 50%;
        top: 50%;
        background-color: #fff;
        border-radius: 3px;
        padding-left:10px;
    }
    .form-item{
        /*color: #fff;*/
        font-size: 16px;
        border: 0;
        padding: 8px;
        line-height: 40px !important;
    }
    .form-item.button{
        position: absolute;
        right: 0;
        bottom: 0;
        top: 0;
        color: #ffffff;
        padding: 8px 15px;
        margin-left: 10px;
        width: 100px;
        height: 100%;
    }
    .form-item.input::-webkit-input-placeholder,
    .form-item.input::-ms-input-placeholder,
    .form-item.input::-moz-input-placeholder,{
        color: #f3f3f3;
    }');
    }

    protected function addScript()
    {
        $script = <<<'JS'
(function() {
    var storage = localStorage || {setItem:function () {}, getItem: function () {}},
        key = 'dcat-admin-lock-screen',
        mode = storage.getItem(key)
 $(function(){
        $('.form-mock').form({
        validate: true,
        success: function (data) {
            if (! data.status) {
                Dcat.error(data.message);
                return false;
            }
            Dcat.success(data.message);
            $('input[name="lockpass"]').val('');
            storage.setItem(key,0);
            $('.mock').css('display','none');
            return false;
        },
        error: function (response) {
            var errorData = JSON.parse(response.responseText);
            if (errorData) {
                Dcat.error(errorData.message);
            } else {
                console.log('提交出错', response.responseText);
            }
            return false;
        },
    })
     })
})()
JS;

        Admin::script($script, true);
    }
}
