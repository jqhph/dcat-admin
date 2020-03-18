<?php

namespace Dcat\Admin\Layout;

class Assets
{
    /**
     * 别名.
     *
     * @var array
     */
    protected $alias = [
        '@nunito' => [
            'css' => ['https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,800,800i,900,900i'],
        ],
        '@montserrat' => [
            'css' => ['https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600'],
        ],
        '@dcat' => [
            'js'  => 'dcat-admin/dcat/js/dcat-app.js',
            'css' => 'dcat-admin/dcat/css/dcat-app.css',
        ],
        '@vendors' => [
            'js'  => 'dcat-admin/vendors/js/vendors.min.js',
            'css' => 'dcat-admin/vendors/css/vendors.min.css',
        ],
        '@bootstrap' => [
            'css' => 'dcat-admin/css/bootstrap.css',
        ],
        '@bootstrap-extended' => [
            'css' => 'dcat-admin/css/bootstrap-extended.css',
        ],
        '@default-colors' => [
            'css' => 'dcat-admin/css/colors.css',
        ],
        '@menu' => [
            'js' => 'dcat-admin/js/core/app-menu.js',
        ],
        '@app' => [
            'js' => 'dcat-admin/js/core/app.js',
        ],
        '@components' => [
            'css' => 'dcat-admin/css/components.css',
        ],
        '@palette-gradient' => [
            'css' => 'dcat-admin/css/core/colors/palette-gradient.css',
        ],
        '@datatables' => [
            'css' => 'dcat-admin/vendors/css/tables/datatable/datatables.min.css',
        ],
        '@data-list-view' => [
            'css' => 'dcat-admin/css/pages/data-list-view.css',
        ],
        '@custom' => [
            'css' => 'dcat-admin/css/custom-laravel.css',
        ],
        '@grid-extension' => [
            'js' => 'dcat-admin/dcat/extra/grid-extend.js',
        ],
        '@resource-selector' => [
            'js' => 'dcat-admin/dcat/extra/resource-selector.js',
        ],
        '@layer' => [
            'js' => 'dcat-admin/dcat/plugins/layer/layer.js',
        ],
        '@pjax' => [
            'js' => 'dcat-admin/dcat/plugins/jquery-pjax/jquery.pjax.min.js',
        ],
        '@toastr' => [
            'js'  => 'dcat-admin/vendors/js/extensions/toastr.min.js',
            'css' => 'dcat-admin/vendors/css/extensions/toastr.css',
        ],
        '@jquery.nestable' => [
            'js'  => 'dcat-admin/dcat/plugins/nestable/jquery.nestable.min.js',
            'css' => 'dcat-admin/dcat/plugins/nestable/nestable.css',
        ],
        '@select2' => [
            'js'  => 'dcat-admin/vendors/js/forms/select/select2.full.min.js',
            'css' => 'dcat-admin/vendors/css/forms/select/select2.min.css',
        ],
        '@bootstrap-datetimepicker' => [
            'js'  => 'dcat-admin/dcat/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js',
            'css' => 'dcat-admin/dcat/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css',
        ],
        '@rwd-table' => [
            'js'  => 'dcat-admin/dcat/plugins/RWD-Table-Patterns/dist/js/rwd-table.min.js',
            'css' => 'dcat-admin/dcat/plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css',
        ],
        '@jstree' => [
            'js'  => 'dcat-admin/dcat/plugins/jstree-theme/jstree.min.js',
            'css' => 'dcat-admin/dcat/plugins/jstree-theme/themes/proton/style.min.css',
        ],
        '@switchery' => [
            'js'  => 'dcat-admin/dcat/plugins/switchery/switchery.min.js',
            'css' => 'dcat-admin/dcat/plugins/switchery/switchery.min.css',
        ],
        '@webuploader' => [
            'js' => [
                'vendor/dcat-admin/webuploader/webuploader.min.js',
                'vendor/dcat-admin/dcat-admin/upload.min.js',
            ],
            'css' => 'vendor/dcat-admin/webuploader/webuploader.min.css',
        ],
        '@chartjs' => [

        ],
        '@jquery.sparkline' => [

        ],
        '@jquery.bootstrap-duallistbox' => [

        ],
        '@number-input' => [

        ],
        '@ionslider' => [
            'js' => [
                '/vendor/dcat-admin/AdminLTE/plugins/ionslider/ion.rangeSlider.min.js',
            ],
            'css' => [
                '/vendor/dcat-admin/AdminLTE/plugins/ionslider/ion.rangeSlider.css',
                '/vendor/dcat-admin/AdminLTE/plugins/ionslider/ion.rangeSlider.skinNice.css',
            ],
        ],
    ];

    /**
     * js代码.
     *
     * @var array
     */
    protected $script = [];

    /**
     * css代码.
     *
     * @var array
     */
    protected $style = [];

    /**
     * css脚本路径.
     *
     * @var array
     */
    protected $css = [];

    /**
     * js脚本路径.
     *
     * @var array
     */
    protected $js = [];

    /**
     * 在head标签内加载的js脚本.
     *
     * @var array
     */
    protected $headerJs = [
        'vendors' => '@vendors',
        'dcat'    => '@dcat',
    ];

    /**
     * 基础css.
     *
     * @var array
     */
    protected $baseCss = [
        'vendors'            => '@vendors',
        'bootstrap'          => '@bootstrap',
        'bootstrap-extended' => '@bootstrap-extended',
        'toastr'             => '@toastr',
        'components'         => '@components',
        'palette-gradient'   => '@palette-gradient',
        'colors'             => '@default-colors',
        //'custom'             => 'custom',

        'datatables'     => '@datatables',
        'data-list-view' => '@data-list-view',
        'dcat'           => '@dcat',
    ];

    /**
     * 基础js.
     *
     * @var array
     */
    protected $baseJs = [
        'menu'   => '@menu',
        'app'    => '@app',
        'toastr' => '@toastr',
        'pjax'   => '@pjax',
        'layer'  => '@layer',
    ];

    /**
     * @var array
     */
    public $fonts = [
        '@nunito',
        '@montserrat',
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
        'dark'      => 'dark-layout',
        'semi-dark' => 'semi-dark-layout',
    ];

    /**
     * Assets constructor.
     */
    public function __construct()
    {
        $this->isPjax = request()->pjax();
    }

    /**
     * 设置或获取别名.
     *
     * @param string|array $name
     * @param string|array $js
     * @param string|array $css
     *
     * @return void|array
     */
    public function alias($name, $js = null, $css = null)
    {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->alias($key, $value['js'] ?? [], $value['css'] ?? []);
            }

            return;
        }

        if ($js === null && $css === null) {
            return $this->alias[$name] ?? [];
        }

        if (strpos($name, '@') !== 0) {
            $name = '@'.$name;
        }

        $this->alias[$name] = [
            'js'  => $js,
            'css' => $css,
        ];
    }

    /**
     * 使用全页面(无菜单和导航栏).
     *
     * @param bool $value
     *
     * @return $this
     */
    public function full(bool $value = true)
    {
        $this->usingFullPage = $value;

        return $this;
    }

    /**
     * 根据别名设置需要载入的js和css脚本.
     *
     * @param string $alias
     * @param string $type
     */
    public function collect(string $alias, string $type = '')
    {
        if (strpos($alias, '@') !== 0) {
            $alias = '@'.$alias;
        }

        if ($type === 'js') {
            $this->js($this->alias[$alias]['js'] ?? null);

            return;
        } elseif ($type === 'css') {
            $this->css($this->alias[$alias]['css'] ?? null);

            return;
        }

        $this->js($this->alias[$alias]['js'] ?? null);
        $this->css($this->alias[$alias]['css'] ?? null);
    }

    /**
     * 设置需要载入的css脚本.
     *
     * @param string|array $css
     */
    public function css($css)
    {
        if (! $css) {
            return;
        }
        $this->css = array_merge(
            $this->css,
            (array) $css
        );
    }

    /**
     * 设置需要载入的基础css脚本.
     *
     * @param array $css
     */
    public function baseCss(array $css)
    {
        $this->baseCss = $css;
    }

    /**
     * 设置需要载入的js脚本.
     *
     * @param string|array $js
     */
    public function js($js)
    {
        if (! $js) {
            return;
        }
        $this->js = array_merge(
            $this->js,
            (array) $js
        );
    }

    /**
     * 获取脚本的真实路径
     *
     * @param string $path
     * @param string $type
     *
     * @return string|array|null
     */
    public function getRealPath($path, string $type = 'js')
    {
        if (empty($this->alias[$path])) {
            return admin_asset($path);
        }

        $paths = isset($this->alias[$path][$type]) ? (array) $this->alias[$path][$type] : null;

        if (! $paths) {
            return $paths;
        }

        foreach ($paths as &$value) {
            $value = admin_asset($value);
        }

        return $paths;
    }

    /**
     * 设置在head标签内加载的js.
     *
     * @param string|array $js
     */
    public function headerJs($js)
    {
        if (! $js) {
            return;
        }
        $this->headerJs = array_merge($this->headerJs, (array) $js);
    }

    /**
     * 设置基础js脚本.
     *
     * @param array $js
     */
    public function baseJs(array $js)
    {
        $this->baseJs = $js;
    }

    /**
     * 设置js代码.
     *
     * @param string|array $script
     */
    public function script($script)
    {
        if (! $script) {
            return;
        }
        $this->script = array_merge($this->script, (array) $script);
    }

    /**
     * 设置css代码.
     *
     * @param string $style
     */
    public function style($style)
    {
        if (! $style) {
            return;
        }
        $this->style = array_merge($this->style, (array) $style);
    }

    /**
     * 增加布局css文件.
     */
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

    /**
     * 主题css文件.
     */
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

    /**
     * 字体css脚本路径.
     */
    protected function addFontCss()
    {
        $this->fonts && (
            $this->baseCss = array_merge(
                $this->baseCss,
                (array) $this->fonts
            )
        );
    }

    /**
     * 合并基础css脚本.
     */
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

    /**
     * @return string
     */
    public function renderCss()
    {
        $this->mergeBaseCss();

        $html = '';

        foreach (array_unique($this->css) as &$v) {
            if (! $paths = $this->getRealPath($v, 'css')) {
                continue;
            }

            foreach ((array) $paths as $path) {
                $html .= "<link rel=\"stylesheet\" href=\"{$path}\">";
            }
        }

        return $html;
    }

    /**
     * 合并基础js脚本.
     */
    protected function mergeBaseJs()
    {
        if ($this->isPjax) {
            return;
        }

        if ($this->usingFullPage) {
            unset($this->baseJs['menu']);
        }

        $this->js = array_merge($this->baseJs, $this->js);
    }

    /**
     * @return string
     */
    public function renderJs()
    {
        $this->mergeBaseJs();

        $html = '';

        foreach (array_unique($this->js) as &$v) {
            if (! $paths = $this->getRealPath($v, 'js')) {
                continue;
            }

            foreach ((array) $paths as $path) {
                $html .= "<script src=\"{$path}\"></script>";
            }
        }

        return $html;
    }

    /**
     * @return string
     */
    public function renderHeaderJs()
    {
        $html = '';

        foreach (array_unique($this->headerJs) as &$v) {
            if (! $paths = $this->getRealPath($v, 'js')) {
                continue;
            }

            foreach ((array) $paths as $path) {
                $html .= "<script src=\"{$path}\"></script>";
            }
        }

        return $html;
    }

    /**
     * @return string
     */
    public function renderScript()
    {
        $script = implode(';', array_unique($this->script));

        return "<script data-exec-on-popstate>Dcat.ready(function () { {$script} });</script>";
    }

    /**
     * @return string
     */
    public function renderStyle()
    {
        $style = implode('', array_unique($this->style));

        return "<style>$style</style>";
    }
}
