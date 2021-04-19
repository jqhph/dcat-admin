<?php

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\Response;

if (! function_exists('admin_setting')) {
    /**
     * 获取或保存配置参数.
     *
     * @param string|array $key
     * @param mixed         $default
     *
     * @return \Dcat\Admin\Support\Setting|mixed
     */
    function admin_setting($key = null, $default = null)
    {
        if ($key === null) {
            return app('admin.setting');
        }

        if (is_array($key)) {
            app('admin.setting')->save($key);

            return;
        }

        return app('admin.setting')->get($key, $default);
    }
}

if (! function_exists('admin_setting_array')) {
    /**
     * 获取配置参数并转化为数组格式.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return \Dcat\Admin\Support\Setting|mixed
     */
    function admin_setting_array(?string $key, $default = [])
    {
        return app('admin.setting')->getArray($key, $default);
    }
}

if (! function_exists('admin_extension_setting')) {
    /**
     * 获取扩展配置参数.
     *
     * @param string       $extension
     * @param string|array $key
     * @param mixed        $default
     *
     * @return mixed
     */
    function admin_extension_setting($extension, $key = null, $default = null)
    {
        $extension = app($extension);

        if ($extension instanceof Dcat\Admin\Extend\ServiceProvider) {
            return $extension->config($key, $default);
        }
    }
}

if (! function_exists('admin_section')) {
    /**
     * Get the string contents of a section.
     *
     * @param string $section
     * @param mixed  $default
     * @param array  $options
     *
     * @return mixed
     */
    function admin_section(string $section, $default = null, array $options = [])
    {
        return app('admin.sections')->yieldContent($section, $default, $options);
    }
}

if (! function_exists('admin_has_section')) {
    /**
     * Check if section exists.
     *
     * @param string $section
     *
     * @return mixed
     */
    function admin_has_section(string $section)
    {
        return app('admin.sections')->hasSection($section);
    }
}

if (! function_exists('admin_inject_section')) {
    /**
     * Injecting content into a section.
     *
     * @param string $section
     * @param mixed  $content
     * @param bool   $append
     * @param int    $priority
     */
    function admin_inject_section(string $section, $content = null, bool $append = true, int $priority = 10)
    {
        app('admin.sections')->inject($section, $content, $append, $priority);
    }
}

if (! function_exists('admin_inject_section_if')) {
    /**
     * Injecting content into a section.
     *
     * @param mixed  $condition
     * @param string $section
     * @param mixed  $content
     * @param bool   $append
     * @param int    $priority
     */
    function admin_inject_section_if($condition, $section, $content = null, bool $append = false, int $priority = 10)
    {
        if ($condition) {
            app('admin.sections')->inject($section, $content, $append, $priority);
        }
    }
}

if (! function_exists('admin_has_default_section')) {
    /**
     * Check if default section exists.
     *
     * @param string $section
     *
     * @return mixed
     */
    function admin_has_default_section(string $section)
    {
        return app('admin.sections')->hasDefaultSection($section);
    }
}

if (! function_exists('admin_inject_default_section')) {
    /**
     * Injecting content into a section.
     *
     * @param string                              $section
     * @param string|Renderable|Htmlable|callable $content
     */
    function admin_inject_default_section(string $section, $content)
    {
        app('admin.sections')->injectDefault($section, $content);
    }
}

if (! function_exists('admin_trans_field')) {
    /**
     * Translate the field name.
     *
     * @param $field
     * @param null $locale
     *
     * @return array|\Illuminate\Contracts\Translation\Translator|null|string
     */
    function admin_trans_field($field, $locale = null)
    {
        return app('admin.translator')->transField($field, $locale);
    }
}

if (! function_exists('admin_trans_label')) {
    /**
     * Translate the label.
     *
     * @param $label
     * @param array $replace
     * @param null  $locale
     *
     * @return array|\Illuminate\Contracts\Translation\Translator|null|string
     */
    function admin_trans_label($label = null, $replace = [], $locale = null)
    {
        return app('admin.translator')->transLabel($label, $replace, $locale);
    }
}

if (! function_exists('admin_trans_option')) {
    /**
     * Translate the field name.
     *
     * @param $field
     * @param array $replace
     * @param null  $locale
     *
     * @return array|\Illuminate\Contracts\Translation\Translator|null|string
     */
    function admin_trans_option($optionValue, $field, $replace = [], $locale = null)
    {
        $slug = admin_controller_slug();

        return admin_trans("{$slug}.options.{$field}.{$optionValue}", $replace, $locale);
    }
}

if (! function_exists('admin_trans')) {
    /**
     * Translate the given message.
     *
     * @param string $key
     * @param array  $replace
     * @param string $locale
     *
     * @return \Illuminate\Contracts\Translation\Translator|string|array|null
     */
    function admin_trans($key, $replace = [], $locale = null)
    {
        return app('admin.translator')->trans($key, $replace, $locale);
    }
}

if (! function_exists('admin_controller_slug')) {
    /**
     * @return string
     */
    function admin_controller_slug()
    {
        static $slug = [];

        $controller = admin_controller_name();

        return $slug[$controller] ?? ($slug[$controller] = Helper::slug($controller));
    }
}

if (! function_exists('admin_controller_name')) {
    /**
     * Get the class "basename" of the current controller.
     *
     * @return string
     */
    function admin_controller_name()
    {
        return Helper::getControllerName();
    }
}

if (! function_exists('admin_path')) {
    /**
     * Get admin path.
     *
     * @param string $path
     *
     * @return string
     */
    function admin_path($path = '')
    {
        return ucfirst(config('admin.directory')).($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (! function_exists('admin_url')) {
    /**
     * Get admin url.
     *
     * @param string $path
     * @param mixed  $parameters
     * @param bool   $secure
     *
     * @return string
     */
    function admin_url($path = '', $parameters = [], $secure = null)
    {
        if (url()->isValidUrl($path)) {
            return $path;
        }

        $secure = $secure ?: (config('admin.https') || config('admin.secure'));

        return url(admin_base_path($path), $parameters, $secure);
    }
}

if (! function_exists('admin_base_path')) {
    /**
     * Get admin url.
     *
     * @param string $path
     *
     * @return string
     */
    function admin_base_path($path = '')
    {
        $prefix = '/'.trim(config('admin.route.prefix'), '/');

        $prefix = ($prefix == '/') ? '' : $prefix;

        $path = trim($path, '/');

        if (is_null($path) || strlen($path) == 0) {
            return $prefix ?: '/';
        }

        return $prefix.'/'.$path;
    }
}

if (! function_exists('admin_toastr')) {
    /**
     * Flash a toastr message bag to session.
     *
     * @param string $message
     * @param string $type
     * @param array $options
     */
    function admin_toastr($message = '', $type = 'success', $options = [])
    {
        $toastr = new MessageBag(get_defined_vars());

        session()->flash('dcat-admin-toastr', $toastr);
    }
}

if (! function_exists('admin_success')) {
    /**
     * Flash a success message bag to session.
     *
     * @param string $title
     * @param string $message
     */
    function admin_success($title, $message = '')
    {
        admin_info($title, $message, 'success');
    }
}

if (! function_exists('admin_error')) {
    /**
     * Flash a error message bag to session.
     *
     * @param string $title
     * @param string $message
     */
    function admin_error($title, $message = '')
    {
        admin_info($title, $message, 'error');
    }
}

if (! function_exists('admin_warning')) {
    /**
     * Flash a warning message bag to session.
     *
     * @param string $title
     * @param string $message
     */
    function admin_warning($title, $message = '')
    {
        admin_info($title, $message, 'warning');
    }
}

if (! function_exists('admin_info')) {
    /**
     * Flash a message bag to session.
     *
     * @param string $title
     * @param string $message
     * @param string $type
     */
    function admin_info($title, $message = '', $type = 'info')
    {
        $message = new MessageBag(get_defined_vars());

        session()->flash($type, $message);
    }
}

if (! function_exists('admin_asset')) {
    /**
     * @param $path
     *
     * @return string
     */
    function admin_asset($path)
    {
        return Admin::asset()->url($path);
    }
}

if (! function_exists('admin_route')) {
    /**
     * 根据路由别名获取url.
     *
     * @param string|null $route
     * @param array $params
     * @param bool $absolute
     *
     * @return string
     */
    function admin_route(?string $route, array $params = [], $absolute = true)
    {
        return Admin::app()->getRoute($route, $params, $absolute);
    }
}

if (! function_exists('admin_route_name')) {
    /**
     * 获取路由别名.
     *
     * @param string|null $route
     *
     * @return string
     */
    function admin_route_name(?string $route)
    {
        return Admin::app()->getRoutePrefix().$route;
    }
}

if (! function_exists('admin_api_route_name')) {
    /**
     * 获取api的路由别名.
     *
     * @param string $route
     *
     * @return string
     */
    function admin_api_route_name(?string $route = '')
    {
        return Admin::app()->getCurrentApiRoutePrefix().$route;
    }
}

if (! function_exists('admin_extension_path')) {
    /**
     * @param string|null $path
     *
     * @return string
     */
    function admin_extension_path(?string $path = null)
    {
        $dir = rtrim(config('admin.extension.dir'), '/') ?: base_path('dcat-admin-extensions');

        $path = ltrim($path, '/');

        return $path ? $dir.'/'.$path : $dir;
    }
}

if (! function_exists('admin_color')) {
    /**
     * @param string|null $color
     *
     * @return string|\Dcat\Admin\Color
     */
    function admin_color(?string $color = null)
    {
        if ($color === null) {
            return Admin::color();
        }

        return Admin::color()->get($color);
    }
}

if (! function_exists('admin_view')) {
    /**
     * @param string $view
     * @param array  $data
     *
     * @return string
     *
     * @throws \Throwable
     */
    function admin_view($view, array $data = [])
    {
        return Admin::view($view, $data);
    }
}

if (! function_exists('admin_script')) {
    /**
     * @param string $js
     * @param bool   $direct
     *
     * @return void
     */
    function admin_script($script, bool $direct = false)
    {
        Admin::script($script, $direct);
    }
}

if (! function_exists('admin_style')) {
    /**
     * @param string $style
     *
     * @return void
     */
    function admin_style($style)
    {
        Admin::style($style);
    }
}

if (! function_exists('admin_js')) {
    /**
     * @param string|array $js
     *
     * @return void
     */
    function admin_js($js)
    {
        Admin::js($js);
    }
}

if (! function_exists('admin_css')) {
    /**
     * @param string|array $css
     *
     * @return void
     */
    function admin_css($css)
    {
        Admin::css($css);
    }
}

if (! function_exists('admin_require_assets')) {
    /**
     * @param string|array $asset
     *
     * @return void
     */
    function admin_require_assets($asset)
    {
        Admin::requireAssets($asset);
    }
}

if (! function_exists('admin_javascript')) {
    /**
     * 暂存JS代码，并使用唯一字符串代替.
     *
     * @param string $scripts
     *
     * @return string
     */
    function admin_javascript(string $scripts)
    {
        return Dcat\Admin\Support\JavaScript::make($scripts);
    }
}

if (! function_exists('admin_javascript_json')) {
    /**
     * @param array|object $data
     *
     * @return string
     */
    function admin_javascript_json($data)
    {
        return Dcat\Admin\Support\JavaScript::format($data);
    }
}

if (! function_exists('admin_exit')) {
    /**
     * 响应数据并中断后续逻辑.
     *
     * @param Response|string|array $response
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    function admin_exit($response = '')
    {
        Admin::exit($response);
    }
}
