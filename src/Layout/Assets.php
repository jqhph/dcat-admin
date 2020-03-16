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
        'dcat'    => 'dcat-admin/dcat/js/dcat-app.js',
    ];

    /**
     * @var array
     */
    protected $baseCss = [
        'vendors'            => 'dcat-admin/vendors/css/vendors.min.css',
        'bootstrap'          => 'dcat-admin/css/bootstrap.css',
        'bootstrap-extended' => 'dcat-admin/css/bootstrap-extended.css',
        'toastr'             => 'dcat-admin/vendors/css/extensions/toastr.css',
        'components'         => 'dcat-admin/css/components.css',
        'palette-gradient'   => 'dcat-admin/css/core/colors/palette-gradient.css',
        'colors'             => 'dcat-admin/css/colors.css',
        //'custom'             => 'dcat-admin/css/custom-laravel.css',

        'datatables' => 'dcat-admin/vendors/css/tables/datatable/datatables.min.css',
        'data-list-view' => 'dcat-admin/css/pages/data-list-view.css',

        'dcat'               => 'dcat-admin/dcat/css/dcat-app.css',
    ];

    /**
     * @var array
     */
    protected $baseJs = [
        'menu'   => 'dcat-admin/js/core/app-menu.js',
        'app'    => 'dcat-admin/js/core/app.js',
        'toastr' => 'dcat-admin/vendors/js/extensions/toastr.min.js',
        'pjax'   => 'dcat-admin/dcat/plugins/jquery-pjax/jquery.pjax.min.js',
        'layer'  => 'dcat-admin/dcat/plugins/layer/layer.js',
    ];

    /**
     * @var array
     */
    public $components = [
        'jquery.nestable' => [
            'js'  => 'dcat-admin/dcat/plugins/nestable/jquery.nestable.min.js',
            'css' => 'dcat-admin/dcat/plugins/nestable/nestable.css',
        ],
        'select2' => [
            'js'  => 'dcat-admin/vendors/js/forms/select/select2.full.min.js',
            'css' => 'dcat-admin/vendors/css/forms/select/select2.min.css',
        ],
        'bootstrap-datetimepicker' => [
            'js'  => 'dcat-admin/dcat/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js',
            'css' => 'dcat-admin/dcat/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css',
        ],
        'rwd-table' => [
            'js'  => 'dcat-admin/dcat/plugins/RWD-Table-Patterns/dist/js/rwd-table.min.js',
            'css' => 'dcat-admin/dcat/plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css',
        ],
        'jstree' => [
            'js'  => 'dcat-admin/dcat/plugins/jstree-theme/jstree.min.js',
            'css' => 'dcat-admin/dcat/plugins/jstree-theme/themes/proton/style.min.css',
        ],
    ];

    /**
     * @var array
     */
    public $fonts = [
        'https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,800,800i,900,900i',
        'https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600',
    ];

    /**
     * @var bool
     */
    protected $isPjax = false;

    /**
     * @var bool
     */
    protected $usingFullPage = false;

    /**
     * @var array
     */
    protected $themeCssMap = [
        'dark' => 'dark-layout',
        'semi-dark' => 'semi-dark-layout',
    ];

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

    public function collect(string $name, string $type = '')
    {
        if ($type === 'js') {
            $this->js($this->components[$name]['js'] ?? null);

            return;
        } elseif ($type === 'css') {
            $this->css($this->components[$name]['css'] ?? null);

            return;
        }

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

        $css = $this->themeCssMap[$theme] ?? $theme;

        if ($css === 'light') {
            return;
        }

        $this->baseCss[] = "dcat-admin/css/themes/{$css}.css";
    }

    protected function addFontCss()
    {
        $this->fonts && (
            $this->baseCss = array_merge(
                $this->baseCss,
                (array) $this->fonts
            )
        );
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
