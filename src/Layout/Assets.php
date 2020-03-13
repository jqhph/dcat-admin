<?php

namespace Dcat\Admin\Layout;

class Assets
{
    /**
     * @var array
     */
    protected $script = [];

    /**
     * @var array
     */
    protected $style = [];

    /**
     * @var array
     */
    protected $css = [];

    /**
     * @var array
     */
    protected $js = [];

    /**
     * @var array
     */
    protected $headerJs = [
        'vendors' => 'dcat-admin/vendors/js/vendors.min.js',
        'dcat'    => 'dcat-admin/dcat/js/app.js',
    ];

    /**
     * @var array
     */
    protected $baseCss = [
        'vendors'            => 'dcat-admin/vendors/css/vendors.min.css',
        'bootstrap'          => 'dcat-admin/css/bootstrap.css',
        'bootstrap-extended' => 'dcat-admin/css/bootstrap-extended.css',
        'toastr'             => 'dcat-admin/vendors/css/extensions/toastr.css',
        'colors'             => 'dcat-admin/css/colors.css',
        'components'         => 'dcat-admin/css/components.css',
        'palette-gradient'   => 'dcat-admin/css/core/colors/palette-gradient.css',
        //'custom'             => 'dcat-admin/css/custom-laravel.css',
        'dcat'               => 'dcat-admin/dcat/css/app.css',
    ];

    /**
     * @var array
     */
    protected $baseJs = [
        'menu'       => 'dcat-admin/js/core/app-menu.js',
        'app'        => 'dcat-admin/js/core/app.js',
        'toastr'     => 'dcat-admin/vendors/js/extensions/toastr.min.js',
        'pjax'       => 'dcat-admin/plugins/jquery-pjax/jquery.pjax.min.js',
    ];

    /**
     * @var array
     */
    public $components = [];

    /**
     * @var string
     */
    public $fonts = 'https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,800,800i,900,900i';

    /**
     * @var bool
     */
    protected $isPjax = false;

    /**
     * @var bool
     */
    protected $usingFullPage = false;

    /**
     * Assets constructor.
     */
    public function __construct()
    {
        $this->isPjax = request()->pjax();
    }

    public function full(bool $value = true)
    {
        $this->usingFullPage = $value;

        return $this;
    }

    public function collect(string $name)
    {
        $this->js($this->components[$name]['js'] ?? null);
        $this->css($this->components[$name]['css'] ?? null);
    }

    public function css($css)
    {
        if (! $css) {
            return;
        }
        $this->css = array_merge($this->css, (array) $css);
    }

    public function baseCss(array $css)
    {
        $this->baseCss = $css;
    }

    public function js($js)
    {
        if (! $js) {
            return;
        }
        $this->js = array_merge($this->js, (array) $js);
    }

    public function headerJs($js)
    {
        if (! $js) {
            return;
        }
        $this->headerJs = array_merge($this->headerJs, (array) $js);
    }

    public function baseJs(array $js)
    {
        if (! $js) {
            return;
        }
        $this->baseJs = $js;
    }

    public function script($script)
    {
        if (! $script) {
            return;
        }
        $this->script = array_merge($this->script, (array) $script);
    }

    public function style($style)
    {
        if (! $style) {
            return;
        }
        $this->style = array_merge($this->style, (array) $style);
    }

    protected function addLayoutCss()
    {
        if ($this->usingFullPage) {
            return;
        }

        if (config('admin.layout.main_layout_type') === 'horizontal') {
            $this->baseCss[] = 'dcat-admin/css/core/menu/menu-types/horizontal-menu.css';
        }

        $this->baseCss[] = 'dcat-admin/css/core/menu/menu-types/vertical-menu.css';
    }

    protected function addThemeCss()
    {
        if (! $theme = config('admin.layout.theme')) {
            return;
        }

        $this->baseCss[] = "dcat-admin/css/themes/{$theme}.css";
    }

    protected function addFontCss()
    {
        $this->fonts && ($this->baseCss[] = $this->fonts);
    }

    protected function mergeBaseCss()
    {
        if ($this->isPjax) {
            return;
        }

        $this->addLayoutCss();
        $this->addThemeCss();
        $this->addFontCss();

        $this->css = array_merge($this->baseCss, $this->css);
    }

    public function renderCss()
    {
        $this->mergeBaseCss();

        $html = '';

        foreach (array_unique($this->css) as &$v) {
            $v = admin_asset($v);

            $html .= "<link rel=\"stylesheet\" href=\"{$v}\">";
        }

        return $html;
    }

    protected function mergeBaseJs()
    {
        if ($this->isPjax) {
            return;
        }

        $this->js = array_merge($this->baseJs, $this->js);
    }

    public function renderJs()
    {
        $this->mergeBaseJs();

        $html = '';

        foreach (array_unique($this->js) as &$v) {
            $v = admin_asset($v);

            $html .= "<script src=\"$v\"></script>";
        }

        return $html;
    }

    public function renderHeaderJs()
    {
        $html = '';

        foreach (array_unique($this->headerJs) as &$v) {
            $v = admin_asset($v);

            $html .= "<script src=\"$v\"></script>";
        }

        return $html;
    }

    public function renderScript()
    {
        $script = implode(';', array_unique($this->script));

        return "<script data-exec-on-popstate>Dcat.ready(function () { {$script} });</script>";
    }

    public function renderStyle()
    {
        $style = implode('', array_unique($this->style));

        return "<style>$style</style>";
    }
}
