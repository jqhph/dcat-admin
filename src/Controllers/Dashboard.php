<?php

namespace Dcat\Admin\Controllers;

use Dcat\Admin\Admin;
use Dcat\Admin\Widgets\Tab;
use Illuminate\Support\Arr;

class Dashboard
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function title()
    {
        return view('admin::dashboard.title');
    }

    public static function tab()
    {
        return Tab::make()
            ->padding(0)
            ->custom()
            ->add('Environment', static::environment())
            ->add('Extensions', static::extensions())
            ->add('Dependencies', static::dependencies());
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function environment()
    {
        $envs = [
            ['name' => 'PHP version',       'value' => 'PHP/'.PHP_VERSION],
            ['name' => 'Laravel version',   'value' => app()->version()],
            ['name' => 'CGI',               'value' => php_sapi_name()],
            ['name' => 'Uname',             'value' => php_uname()],
            ['name' => 'Server',            'value' => Arr::get($_SERVER, 'SERVER_SOFTWARE')],

            ['name' => 'Cache driver',      'value' => config('cache.default')],
            ['name' => 'Session driver',    'value' => config('session.driver')],
            ['name' => 'Queue driver',      'value' => config('queue.default')],

            ['name' => 'Timezone',          'value' => config('app.timezone')],
            ['name' => 'Locale',            'value' => config('app.locale')],
            ['name' => 'Env',               'value' => config('app.env')],
            ['name' => 'URL',               'value' => config('app.url')],
        ];

        return view('admin::dashboard.environment', compact('envs'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function extensions()
    {
        $extensions = [
            'Dcat Page' => [
                'name' => '一个简洁的静态站点生成工具 - Dcat Page',
                'link' => 'https://github.com/jqhph/dcat-page',
                'icon' => ' fa-file-text-o',
                'key'  => 'dcat-page',
            ],
            'UEditor' => [
                'name' => '百度在线编辑器 UEditor',
                'link' => 'https://github.com/jqhph/dcat-admin-ueditor',
                'icon' => 'fa-underline',
                'key'  => 'ueditor',
            ],
            '干货集中营' => [
                'name' => '干货集中营',
                'link' => 'https://github.com/jqhph/dcat-admin-gank',
                'icon' => 'fa-newspaper-o',
                'key'  => 'gank',
            ],

        ];

        foreach ($extensions as &$extension) {
            $extension['installed'] = array_key_exists($extension['key'], Admin::extensions());
        }

        return view('admin::dashboard.extensions', compact('extensions'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public static function dependencies()
    {
        $json = file_get_contents(base_path('composer.json'));

        $dependencies = json_decode($json, true)['require'];

        return view('admin::dashboard.dependencies', compact('dependencies'));
    }
}
