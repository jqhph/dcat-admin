<?php


namespace Dcat\Admin\Widgets;


use Dcat\Admin\Admin;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;

class LockScreenSwitcher implements Renderable
{

    public function render()
    {

        $this->addScript();
        $this->addStyle();

        return <<<HTML
<ul class="nav navbar-nav float-right">
    <li class="dropdown dropdown-user nav-item">
        <a class="dropdown-toggle nav-link">
            <span class="dark-lock-switcher">
                <i class="feather icon-lock"></i>
            </span>
        </a>
    </li>
</ul>
HTML;
    }

    protected function addStyle()
    {
        Admin::style('.dark-lock-switcher{margin:0 5px 0 0;font-size: 1.5rem;cursor: pointer}');
    }

    protected function addScript()
    {
        $script = <<<'JS'
(function() {
    var storage = localStorage || {setItem:function () {}, getItem: function () {}},
        key = 'dcat-admin-lock-screen',
        mode = storage.getItem(key)

    function switchLock() {
        if(mode == 0){
            storage.setItem(key, 1);
            $('.mock').css('display','block');
        }
    }


    $(function(){
    if (mode == 1) {
        $('.mock').css('display','block');
    }
    })


    $('.dark-lock-switcher').off('click').on('click', function () {
            if (!mode && typeof(mode)!="undefined"){
                storage.setItem(key, 0);
            }

            switchLock()
    })
})()
JS;

        Admin::script($script, true);
    }
}
