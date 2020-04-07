<?php

namespace Dcat\Admin\Layout;

class Asset
{
    /**
     * 路径别名.
     *
     * @var array
     */
    protected $pathAlias = [
        // Dcat Admin静态资源路径别名
        '@admin' => 'vendors/dcat-admin',

        // Dcat Acmin扩展静态资源路径别名
        '@extension' => 'vendors/dcat-admin-extensions',
    ];

    /**
     * 别名.
     *
     * @var array
     */
    protected $alias = [
        '@AdminLTE' => [
            'js' => [
                '@admin/AdminLTE/js/adminlte.js',
            ],
            'css' => [
                '@admin/AdminLTE/css/adminlte.css',
            ],
        ],
        '@nunito' => [
            'css' => ['https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,800,800i,900,900i'],
        ],
        '@montserrat' => [
            'css' => ['https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600'],
        ],
        '@dcat' => [
            'js'  => '@admin/dcat/js/dcat-app.js',
            'css' => '@admin/dcat/css/dcat-app.css',
        ],
        '@vendors' => [
            'js'  => '@admin/vendors/js/vendors.min.js',
            'css' => '@admin/vendors/css/vendors.min.css',
        ],
        '@default-colors' => [
            'css' => '@admin/css/colors.css',
        ],
        '@menu' => [
            'js' => '@admin/js/core/app-menu.js',
        ],
        '@app' => [
            'js' => '@admin/js/core/app.js',
        ],
        '@components' => [
            'css' => '@admin/css/components.css',
        ],
        '@palette-gradient' => [
            'css' => '@admin/css/core/colors/palette-gradient.css',
        ],
        '@datatables' => [
            'css' => '@admin/vendors/css/tables/datatable/datatables.min.css',
        ],
        '@custom' => [
            'css' => '@admin/css/custom-laravel.css',
        ],
        '@grid-extension' => [
            'js' => '@admin/dcat/extra/grid-extend.js',
        ],
        '@resource-selector' => [
            'js' => '@admin/dcat/extra/resource-selector.js',
        ],
        '@layer' => [
            'js' => '@admin/dcat/plugins/layer/layer.js',
        ],
        '@pjax' => [
            'js' => '@admin/dcat/plugins/jquery-pjax/jquery.pjax.min.js',
        ],
        '@toastr' => [
            'js'  => '@admin/vendors/js/extensions/toastr.min.js',
            'css' => '@admin/vendors/css/extensions/toastr.css',
        ],
        '@jquery.nestable' => [
            'js'  => '@admin/dcat/plugins/nestable/jquery.nestable.min.js',
            'css' => '@admin/dcat/plugins/nestable/nestable.css',
        ],
        '@validator' => [
            'js' => '@admin/dcat/plugins/bootstrap-validator/validator.min.js',
        ],
        '@select2' => [
            'js'  => '@admin/vendors/js/forms/select/select2.full.min.js',
            'css' => '@admin/vendors/css/forms/select/select2.min.css',
        ],
        '@bootstrap-datetimepicker' => [
            'js'  => '@admin/dcat/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js',
            'css' => '@admin/dcat/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css',
        ],
        '@moment' => [
            'js' => [
                '@admin/dcat/plugins/moment/moment.min.js',
            ],
        ],
        '@moment-timezone' => [
            'js' => [
                '@admin/dcat/plugins/moment/moment-timezone-with-data.min.js',
            ],
        ],
        '@rwd-table' => [
            'js'  => '@admin/dcat/plugins/RWD-Table-Patterns/dist/js/rwd-table.min.js',
            'css' => '@admin/dcat/plugins/RWD-Table-Patterns/dist/css/rwd-table.min.css',
        ],
        '@jstree' => [
            'js'  => '@admin/dcat/plugins/jstree-theme/jstree.min.js',
            'css' => '@admin/dcat/plugins/jstree-theme/themes/proton/style.min.css',
        ],
        '@switchery' => [
            'js'  => '@admin/dcat/plugins/switchery/switchery.min.js',
            'css' => '@admin/dcat/plugins/switchery/switchery.min.css',
        ],
        '@webuploader' => [
            'js' => [
                '@admin/dcat/plugins/webuploader/webuploader.min.js',
                '@admin/dcat/extra/upload.js',
            ],
            'css' => '@admin/dcat/extra/upload.css',
        ],
        '@chartjs' => [
            'js' => '@admin/dcat/plugins/chart.js/chart.bundle.min.js',
        ],
        '@jquery.sparkline' => [
            'js' => '@admin/dcat/plugins/jquery.sparkline/jquery.sparkline.min.js',
        ],
        '@jquery.bootstrap-duallistbox' => [
            'js'  => '@admin/dcat/plugins/bootstrap-duallistbox/dist/jquery.bootstrap-duallistbox.min.js',
            'css' => '@admin/dcat/plugins/bootstrap-duallistbox/dist/bootstrap-duallistbox.min.css',
        ],
        '@number-input' => [
            'js' => '@admin/dcat/plugins/number-input/bootstrap-number-input.js',
        ],
        '@ionslider' => [
            'js' => [
                '@admin/dcat/plugins/ionslider/ion.rangeSlider.min.js',
            ],
            'css' => [
                '@admin/dcat/plugins/ionslider/ion.rangeSlider.css',
                '@admin/dcat/plugins/ionslider/ion.rangeSlider.skinNice.css',
            ],
        ],
        '@editor-md' => [
            'js' => [
                '@admin/dcat/plugins/editor-md/lib/raphael.min.js',
                '@admin/dcat/plugins/editor-md/lib/marked.min.js',
                '@admin/dcat/plugins/editor-md/lib/prettify.min.js',
                '@admin/dcat/plugins/editor-md/lib/underscore.min.js',
                '@admin/dcat/plugins/editor-md/lib/sequence-diagram.min.js',
                '@admin/dcat/plugins/editor-md/lib/flowchart.min.js',
                '@admin/dcat/plugins/editor-md/lib/jquery.flowchart.min.js',
                '@admin/dcat/plugins/editor-md/editormd.min.js',
            ],
            'css' => [
                '@admin/dcat/plugins/editor-md/css/editormd.preview.min.css',
                '@admin/dcat/extra/markdown.css',
            ],
        ],
        '@jquery.inputmask' => [
            'js' => '@admin/dcat/plugins/input-mask/jquery.inputmask.bundle.min.js',
        ],
        '@apex-charts' => [
            'js' => '@admin/vendors/js/charts/apexcharts.min.js',
        ],
        '@smart-wizard' => [
            'js' => '@admin/dcat/plugins/SmartWizard/dist/js/jquery.smartWizard.min.js',
            'css' => '@admin/dcat/extra/step.css',
        ],
        '@fontawesome-iconpicker' => [
            'js' => '@admin/dcat/plugins/fontawesome-iconpicker/dist/js/fontawesome-iconpicker.js',
            'css' => '@admin/dcat/plugins/fontawesome-iconpicker/dist/css/fontawesome-iconpicker.min.css',
        ],
    ];

    /**
     * js代码.
     *
     * @var array
     */
    public $script = [];

    /**
     * css代码.
     *
     * @var array
     */
    public $style = [];

    /**
     * css脚本路径.
     *
     * @var array
     */
    public $css = [];

    /**
     * js脚本路径.
     *
     * @var array
     */
    public $js = [];

    /**
     * 在head标签内加载的js脚本.
     *
     * @var array
     */
    public $headerJs = [
        'vendors' => '@vendors',
        'dcat'    => '@dcat',
    ];

    /**
     * 基础css.
     *
     * @var array
     */
    public $baseCss = [
        'AdminLTE'    => '@AdminLTE',
        'vendors'     => '@vendors',
        'toastr'      => '@toastr',
        'datatables'  => '@datatables',
        'dcat'        => '@dcat',
    ];

    /**
     * 基础js.
     *
     * @var array
     */
    public $baseJs = [
        'AdminLTE'  => '@AdminLTE',
        'toastr'    => '@toastr',
        'pjax'      => '@pjax',
        'validator' => '@validator',
        'layer'     => '@layer',
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

        if (mb_strpos($name, '@') !== 0) {
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
     */
    public function collect(string $alias)
    {
        if (mb_strpos($alias, '@') !== 0) {
            $alias = '@'.$alias;
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
     * 根据别名获取资源路径.
     *
     * @param string $path
     * @param string $type
     *
     * @return string|array|null
     */
    public function get($path, string $type = 'js')
    {
        if (empty($this->alias[$path])) {
            return $this->url($path);
        }

        $paths = isset($this->alias[$path][$type]) ? (array) $this->alias[$path][$type] : null;

        if (! $paths) {
            return $paths;
        }

        foreach ($paths as &$value) {
            $value = $this->url($value);
        }

        return $paths;
    }

    /**
     * 获取静态资源完整URL.
     *
     * @param string $path
     *
     * @return string
     */
    public function url($path)
    {
        if (! $path) {
            return $path;
        }

        $path = $this->getRealPath($path);

        if (mb_strpos($path, '//') === false) {
            $path = config('admin.assets_server').'/'.trim($path, '/');
        }

        return (config('admin.https') || config('admin.secure')) ? secure_asset($path) : asset($path);
    }

    /**
     * 获取真实路径.
     *
     * @param string|null $path
     *
     * @return string|null
     */
    public function getRealPath(?string $path)
    {
        if (! $this->hasAlias($path)) {
            return $path;
        }

        return implode(
            '/',
            array_map(
                function ($v) {
                    $v = $this->pathAlias[$v] ?? $v;

                    if (! $this->hasAlias($v)) {
                        return $v;
                    }

                    return $this->getRealPath($v);
                },
                explode('/', $path)
            )
        );
    }

    /**
     * 判断是否含有别名.
     *
     * @param string $value
     *
     * @return bool
     */
    protected function hasAlias($value)
    {
        return $value && mb_strpos($value, '@') !== false;
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

        $this->addFontCss();

        $this->css = array_merge($this->baseCss, $this->css);
    }

    /**
     * @return string
     */
    public function cssToHtml()
    {
        $this->mergeBaseCss();

        $html = '';

        foreach (array_unique($this->css) as &$v) {
            if (! $paths = $this->get($v, 'css')) {
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
    public function jsToHtml()
    {
        $this->mergeBaseJs();

        $html = '';

        foreach (array_unique($this->js) as &$v) {
            if (! $paths = $this->get($v, 'js')) {
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
    public function headerJsToHtml()
    {
        $html = '';

        foreach (array_unique($this->headerJs) as &$v) {
            if (! $paths = $this->get($v, 'js')) {
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
    public function scriptToHtml()
    {
        $script = implode(';', array_unique($this->script));

        return <<<HTML
<script data-exec-on-popstate>
Dcat.ready(function () { 
    try {
        {$script}
    } catch (e) {
        console.error(e)
    }
 });
 </script>
HTML;
    }

    /**
     * @return string
     */
    public function styleToHtml()
    {
        $style = implode('', array_unique($this->style));

        return "<style>$style</style>";
    }
}
