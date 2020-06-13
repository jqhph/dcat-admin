<?php

namespace Dcat\Admin\Widgets;

use Dcat\Admin\Admin;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Str;

class DarkModeSwitcher implements Renderable
{
    public $defaultDarkMode = false;

    public function __construct(?bool $defaultDarkMode = null)
    {
        $this->defaultDarkMode = is_null($defaultDarkMode) ? Str::contains(config('admin.layout.body_class'), 'dark-mode') : $defaultDarkMode;
    }

    public function render()
    {
        $icon = $this->defaultDarkMode ? 'icon-sun' : 'icon-moon';

        $this->addScript();
        $this->addStyle();

        return <<<HTML
<span class="dark-mode-switcher">
    <i class="feather {$icon}"></i>
</span>
HTML;
    }

    protected function addStyle()
    {
        Admin::style('.dark-mode-switcher{font-size: 1.5rem;cursor: pointer}');
    }

    protected function addScript()
    {
        $default = $this->defaultDarkMode ? 'true' : 'false';
        $darkSidebar = config('admin.layout.sidebar_dark') ? 'true' : 'false';

        $script =
            <<<JS
(function() {
    var storage = localStorage || {setItem:function () {}, getItem: function () {}},
        key = 'dcat-admin-theme-mode',
        mode = storage.getItem(key),
        body = $('body'),
        sidebar = $('.main-menu .main-sidebar'),
        icon = $('.dark-mode-switcher i');
        darkSidebar = {$darkSidebar},
        defaultDark = {$default};
    
    function switchMode(dark) {
        if (dark) {
            body.addClass('dark-mode');
            sidebar.removeClass('sidebar-light-primary').addClass('sidebar-dark-white');
            icon.addClass('icon-sun').removeClass('icon-moon');
            
            return;
        }
        
        body.removeClass('dark-mode');
        icon.removeClass('icon-sun').addClass('icon-moon');
        if (! darkSidebar) {
            sidebar.addClass('sidebar-light-primary').removeClass('sidebar-dark-white');
        }
    }
    
    if (mode === 'dark' || (! mode && defaultDark)) {
        switchMode(true);
    } else if (mode === 'def') {
        switchMode(false)
    }
    
    $('.dark-mode-switcher').off('click').on('click', function () {
        icon.toggleClass('icon-sun icon-moon');
        
        if (icon.hasClass('icon-moon')) {
            switchMode(false);
            
            storage.setItem(key, 'def');
            
        } else {
            storage.setItem(key, 'dark');
            
            switchMode(true)
        }
    })
})()
JS;

        Admin::script($script, true);
    }
}
