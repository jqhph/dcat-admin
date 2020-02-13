<?php

namespace Dcat\Admin\Traits;

trait HasAssets
{
    /**
     * @var array
     */
    public static $script = [];

    /**
     * @var array
     */
    public static $style = [];

    /**
     * @var array
     */
    public static $css = [];

    /**
     * @var array
     */
    public static $js = [];

    /**
     * @var array
     */
    public static $html = [];

    /**
     * @var array
     */
    public static $headerJs = [];

    /**
     * @var array
     */
    public static $baseCss = [
        'bootstrap'     => 'vendor/dcat-admin/AdminLTE/bootstrap/css/bootstrap.min.css',
        'adminLTE'      => 'vendor/dcat-admin/AdminLTE/dist/css/AdminLTE.min.css',
        'font-awesome'  => 'vendor/dcat-admin/font-awesome/css/font-awesome.min.css',
        'icons'         => 'vendor/dcat-admin/dcat-admin/icons.css',
        'main'          => 'vendor/dcat-admin/dcat-admin/main.min.css',
    ];

    /**
     * @var array
     */
    public static $baseJs = [
        'bootstrap'         => 'vendor/dcat-admin/AdminLTE/bootstrap/js/bootstrap.min.js',
        'validator'         => 'vendor/dcat-admin/bootstrap-validator/validator.min.js',
        'jquery.slimscroll' => 'vendor/dcat-admin/AdminLTE/plugins/slimScroll/jquery.slimscroll.min.js',
        'adminLTE'          => 'vendor/dcat-admin/AdminLTE/dist/js/app.min.js',
        'layer'             => 'vendor/dcat-admin/layer/layer.js',
        'jquery.form'       => 'vendor/dcat-admin/jquery-form/dist/jquery.form.min.js',
        'waves'             => 'vendor/dcat-admin/waves/waves.min.js',
        'main'              => 'vendor/dcat-admin/dcat-admin/main.min.js',
        'slider'            => 'vendor/dcat-admin/dcat-admin/slider.min.js',
    ];

    /**
     * @var array
     */
    public static $componentsAssets = [
        'select2' => [
            'js'  => 'vendor/dcat-admin/AdminLTE/plugins/select2/select2.full.min.js',
            'css' => 'vendor/dcat-admin/AdminLTE/plugins/select2/select2.min.css',
        ],
        'jquery.bootstrap-duallistbox' => [
            'js'  => 'vendor/dcat-admin/bootstrap-duallistbox/dist/jquery.bootstrap-duallistbox.min.js',
            'css' => 'vendor/dcat-admin/bootstrap-duallistbox/dist/bootstrap-duallistbox.min.css',
        ],
        'jquery.inputmask' => [
            'js' => 'vendor/dcat-admin/AdminLTE/plugins/input-mask/jquery.inputmask.bundle.min.js',
        ],
        'bootstrap-datetimepicker' => [
            'js'  => 'vendor/dcat-admin/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
            'css' => 'vendor/dcat-admin/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
        ],
        'moment' => [
            'js' => 'vendor/dcat-admin/moment/min/moment-with-locales.min.js',
        ],
        'fontawesome-iconpicker' => [
            'js'  => 'vendor/dcat-admin/fontawesome-iconpicker/dist/js/fontawesome-iconpicker.min.js',
            'css' => 'vendor/dcat-admin/fontawesome-iconpicker/dist/css/fontawesome-iconpicker.min.css',
        ],
        'jstree' => [
            'js' => 'vendor/dcat-admin/jstree-theme/jstree.min.js',
        ],
        'jquery.nestable' => [
            'js'  => 'vendor/dcat-admin/nestable/jquery.nestable.min.js',
            'css' => 'vendor/dcat-admin/nestable/nestable.css',
        ],
        'switchery' => [
            'css' => 'vendor/dcat-admin/switchery/switchery.min.css',
            'js'  => 'vendor/dcat-admin/switchery/switchery.min.js',
        ],
        'editable' => [
            'css' => 'vendor/dcat-admin/bootstrap3-editable/css/bootstrap-editable.css',
            'js'  => 'vendor/dcat-admin/bootstrap3-editable/js/bootstrap-editable.min.js',
        ],
        'chartjs' => [
            'js' => 'vendor/dcat-admin/chart.js/chart.bundle.min.js',
        ],
        'jquery.sparkline' => [
            'js' => 'vendor/dcat-admin/jquery.sparkline/jquery.sparkline.min.js',
        ],
        'jquery.counterup' => [
            'js' => 'vendor/dcat-admin/jquery.counterup/jquery.counterup.min.js',
        ],
        'waypoints' => [
            'js' => 'vendor/dcat-admin/waypoints/waypoints.min.js',
        ],
    ];

    /**
     * @var string
     */
    public static $jQuery = 'vendor/dcat-admin/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js';

    /**
     * @var string
     */
    public static $fonts = 'https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,800,800i,900,900i';

    /**
     * @var bool
     */
    public static $disableSkinCss = false;

    /**
     * Enable cdnjs.
     *
     * @see https://cdnjs.com/
     * @see https://www.bootcdn.cn
     */
    public static function cdn()
    {
        static::$jQuery = 'https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js';

        static::$baseCss['adminLTE'] = 'https://cdn.bootcss.com/admin-lte/2.3.2/css/AdminLTE.min.css';
        static::$baseCss['font-awesome'] = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.0/css/font-awesome.min.css';
        static::$baseCss['bootstrap'] = 'https://cdn.bootcss.com/twitter-bootstrap/3.3.4/css/bootstrap.min.css';

        static::$baseJs['adminLTE'] = 'https://cdn.bootcss.com/admin-lte/2.3.2/js/app.min.js';
        static::$baseJs['bootstrap'] = 'https://cdn.bootcss.com/twitter-bootstrap/3.3.4/js/bootstrap.min.js';
        static::$baseJs['jquery.form'] = 'https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.min.js';
        static::$baseJs['jquery.slimscroll'] = 'https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js';
        static::$baseJs['waves'] = 'https://cdnjs.cloudflare.com/ajax/libs/node-waves/0.7.6/waves.min.js';

        static::$componentsAssets['select2']['js'] = 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js';
        static::$componentsAssets['select2']['css'] = 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css';
        static::$componentsAssets['jquery.bootstrap-duallistbox']['js'] = 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap4-duallistbox/3.0.7/jquery.bootstrap-duallistbox.min.js';
        static::$componentsAssets['jquery.inputmask']['js'] = 'https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.0/jquery.inputmask.bundle.min.js';
        static::$componentsAssets['bootstrap-datetimepicker']['js'] = 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js';
        static::$componentsAssets['bootstrap-datetimepicker']['css'] = 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css';
        static::$componentsAssets['moment']['js'] = 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js';
        static::$componentsAssets['fontawesome-iconpicker']['css'] = 'https://cdnjs.cloudflare.com/ajax/libs/fontawesome-iconpicker/3.0.0/css/fontawesome-iconpicker.min.css';
        static::$componentsAssets['jstree']['js'] = 'https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.4/jstree.min.js';

        static::$componentsAssets['switchery']['js'] = 'https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.js';
        static::$componentsAssets['switchery']['css'] = 'https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.css';
        static::$componentsAssets['editable']['js'] = 'https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.1/bootstrap3-editable/js/bootstrap-editable.min.js';
        static::$componentsAssets['editable']['css'] = 'https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.1/bootstrap-editable/css/bootstrap-editable.css';
        static::$componentsAssets['chartjs']['js'] = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js';
        static::$componentsAssets['jquery.sparkline']['js'] = 'https://cdnjs.cloudflare.com/ajax/libs/jquery-sparklines/2.1.2/jquery.sparkline.min.js';
        static::$componentsAssets['waypoints']['js'] = 'https://cdnjs.cloudflare.com/ajax/libs/waypoints/3.1.1/jquery.waypoints.min.js';
        static::$componentsAssets['jquery.counterup']['js'] = 'https://cdnjs.cloudflare.com/ajax/libs/Counter-Up/1.0.0/jquery.counterup.min.js';
    }

    /**
     * @param string $name
     */
    public static function collectComponentAssets(string $name)
    {
        $js = static::$componentsAssets[$name]['js'] ?? null;
        $css = static::$componentsAssets[$name]['css'] ?? null;

        $js && static::js($js);
        $css && static::css($css);
    }

    /**
     * Add css or get all css.
     *
     * @param null $css
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public static function css($css = null)
    {
        if (! is_null($css)) {
            self::$css = array_merge(self::$css, (array) $css);

            return;
        }

        static::$css = array_merge(static::$css, (array) $css);

        if (! request()->pjax()) {
            static::$css = array_merge(static::baseCss(), static::$css);
        }

        $css = '';
        foreach (array_unique(static::$css) as &$v) {
            $v = admin_asset($v);
            $css .= "<link rel=\"stylesheet\" href=\"{$v}\">";
        }

        return $css;
    }

    /**
     * @param null $css
     *
     * @return array|void
     */
    public static function baseCss($css = null)
    {
        if (! is_null($css)) {
            static::$baseCss = $css;

            return;
        }

        if (! static::$disableSkinCss) {
            $skin = config('admin.skin', 'skin-blue-light');

            array_unshift(static::$baseCss, "vendor/dcat-admin/AdminLTE/dist/css/skins/{$skin}.min.css");
        }

        static::$fonts && (static::$baseCss[] = static::$fonts);

        return static::$baseCss;
    }

    /**
     * Add js or get all js.
     *
     * @param null $js
     *
     * @return mixed
     */
    public static function js($js = null)
    {
        if (! is_null($js)) {
            self::$js = array_merge(self::$js, (array) $js);

            return;
        }

        static::$js = array_merge(static::$js, (array) $js);

        if (! request()->pjax()) {
            static::$js = array_merge(static::baseJs(), static::$js);
        }

        $js = '';
        foreach (array_unique(static::$js) as &$v) {
            $v = admin_asset($v);
            $js .= "<script src=\"$v\"></script>";
        }

        return $js;
    }

    /**
     * @param string $html
     *
     * @return null|string
     */
    public static function html($html = '')
    {
        if (! empty($html)) {
            static::$html = array_merge(static::$html, (array) $html);

            return;
        }

        return implode('', array_unique(static::$html));
    }

    /**
     * Add js or get all js.
     *
     * @param null $js
     *
     * @return mixed
     */
    public static function headerJs($js = null)
    {
        if (! is_null($js)) {
            self::$headerJs = array_merge(self::$headerJs, (array) $js);

            return;
        }

        $js = '';
        foreach (array_unique(static::$headerJs) as &$v) {
            $v = admin_asset($v);
            $js .= "<script src=\"$v\"></script>";
        }

        return $js;
    }

    /**
     * @param null $js
     *
     * @return array|void
     */
    public static function baseJs($js = null)
    {
        if (! is_null($js)) {
            static::$baseJs = $js;

            return;
        }

        return static::$baseJs;
    }

    /**
     * @param string $script
     *
     * @return mixed
     */
    public static function script($script = null)
    {
        if ($script !== null) {
            if ($script) {
                self::$script = array_merge(self::$script, (array) $script);
            }

            return;
        }

        $script = implode(';', array_unique(self::$script));

        return "<script data-exec-on-popstate>LA.ready(function () { {$script} });</script>";
    }

    /**
     * @param string $style
     *
     * @return string|void
     */
    public static function style($style = '')
    {
        if (! empty($style)) {
            self::$style = array_merge(self::$style, (array) $style);

            return;
        }

        $style = implode('', array_unique(self::$style));

        return "<style>$style</style>";
    }

    /**
     * @return string
     */
    public static function jQuery()
    {
        return admin_asset(static::$jQuery);
    }
}
