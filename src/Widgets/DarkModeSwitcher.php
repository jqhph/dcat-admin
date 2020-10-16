<?php

namespace Dcat\Admin\Widgets;

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

        return <<<HTML
<ul class="nav navbar-nav float-right">
    <li class="dropdown dropdown-user nav-item">
        <a class="dropdown-toggle nav-link">
            <span class="dark-mode-switcher">
                <i class="feather {$icon}"></i>
            </span>
        </a>
    </li>
</ul>

<script>
Dcat.darkMode.initSwitcher('.dark-mode-switcher');
</script>
HTML;
    }
}
