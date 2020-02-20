<?php

namespace Dcat\Admin;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

abstract class Extension
{
    const NAME = null;

    /**
     * @var string
     */
    protected $serviceProvider;

    /**
     * @var string
     */
    protected $assets = '';

    /**
     * @var string
     */
    protected $views = '';

    /**
     * @var string
     */
    protected $lang = '';

    /**
     * @var string
     */
    protected $migrations = '';

    /**
     * @var string
     */
    protected $composer = '';

    /**
     * @var array
     */
    protected $menu = [];

    /**
     * @var array
     */
    protected $permission = [];

    /**
     * The menu validation rules.
     *
     * @var array
     */
    protected $menuValidationRules = [
        'title' => 'required',
        'path'  => 'required',
        'icon'  => 'required',
    ];

    /**
     * The permission validation rules.
     *
     * @var array
     */
    protected $permissionValidationRules = [
        'name'  => 'required',
        'slug'  => 'required',
        'path'  => 'required',
    ];

    /**
     * @return string
     */
    final public function getName()
    {
        return static::NAME;
    }

    /**
     * @return string
     */
    public function composer()
    {
        return $this->composer;
    }

    /**
     * @return string
     */
    public function serviceProvider()
    {
        return $this->serviceProvider;
    }

    /**
     * Get the path of assets files.
     *
     * @return string
     */
    public function assets()
    {
        return $this->assets;
    }

    /**
     * Get the path of view files.
     *
     * @return string
     */
    public function views()
    {
        return $this->views;
    }

    /**
     * Get the path of migration files.
     *
     * @return string
     */
    public function migrations()
    {
        return $this->migrations;
    }

    /**
     * @return array
     */
    public function menu()
    {
        return $this->menu;
    }

    /**
     * @return array
     */
    public function permission()
    {
        return $this->permission;
    }

    /**
     * @return string
     */
    public function lang()
    {
        return $this->lang;
    }

    /**
     * Whether the extension is enabled.
     *
     * @return bool
     */
    final public static function enabled()
    {
        return config('admin-extensions.'.static::NAME.'.enable') ? true : false;
    }

    /**
     * Whether the extension is disabled.
     *
     * @return bool
     */
    final public static function disabled()
    {
        return ! static::enabled();
    }

    /**
     * Get config set in config/admin.php.
     *
     * @param string $key
     * @param null   $default
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    final public function config($key = null, $default = null)
    {
        if (is_null($key)) {
            $key = sprintf('admin.extensions.%s', static::NAME);
        } else {
            $key = sprintf('admin.extensions.%s.%s', static::NAME, $key);
        }

        return config($key, $default);
    }

    /**
     * Import menu item and permission to dcat-admin.
     */
    public function import(Command $command)
    {
        if ($menu = $this->menu()) {
            if ($this->validateMenu($menu)) {
                extract($menu);

                if ($this->checkMenuExist($path)) {
                    $command->warn("Menu [$path] already exists!");
                } else {
                    $this->createMenu($title, $path, $icon);
                    $command->info('Import extension menu succeeded!');
                }
            }
        }

        if ($permission = $this->permission()) {
            if ($this->validatePermission($permission)) {
                extract($permission);

                if ($this->checkPermissionExist($slug)) {
                    $command->warn("Permission [$slug] already exists!");
                } else {
                    $this->createPermission($name, $slug, $path);
                    $command->info('Import extension permission succeeded!');
                }
            }
        }
    }

    /**
     * Uninstall the extension.
     *
     * @param Command $command
     */
    public function uninstall(Command $command)
    {
    }

    /**
     * Validate menu fields.
     *
     * @param array $menu
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function validateMenu(array $menu)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($menu, $this->menuValidationRules);

        if ($validator->passes()) {
            return true;
        }

        $message = "Invalid menu:\r\n".implode("\r\n", Arr::flatten($validator->errors()->messages()));

        throw new \Exception($message);
    }

    /**
     * @param $path
     *
     * @return bool
     */
    protected function checkMenuExist($path)
    {
        $menuModel = config('admin.database.menu_model');

        /* @var \Illuminate\Database\Eloquent\Builder $query */
        $query = $menuModel::query();

        $result = $query->where('uri', $path)
            ->get()
            ->first();

        return $result ? true : false;
    }

    /**
     * Validate permission fields.
     *
     * @param array $permission
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function validatePermission(array $permission)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($permission, $this->permissionValidationRules);

        if ($validator->passes()) {
            return true;
        }

        $message = "Invalid permission:\r\n".implode("\r\n", Arr::flatten($validator->errors()->messages()));

        throw new \Exception($message);
    }

    /**
     * Create a item in dcat-admin left side menu.
     *
     * @param string $title
     * @param string $uri
     * @param string $icon
     * @param int    $parentId
     */
    protected function createMenu($title, $uri, $icon = 'fa-bars', $parentId = 0)
    {
        $menuModel = config('admin.database.menu_model');

        $lastOrder = $menuModel::max('order');

        $menuModel::create([
            'parent_id' => $parentId,
            'order'     => $lastOrder + 1,
            'title'     => $title,
            'icon'      => $icon,
            'uri'       => $uri,
        ]);
    }

    /**
     * @param $slug
     *
     * @return bool
     */
    protected function checkPermissionExist($slug)
    {
        $permissionModel = config('admin.database.permissions_model');

        /* @var \Illuminate\Database\Eloquent\Builder $query */
        $query = $permissionModel::query();

        $result = $query->where('slug', $slug)
            ->get()
            ->first();

        return $result ? true : false;
    }

    /**
     * Create a permission for this extension.
     *
     * @param $name
     * @param $slug
     * @param $path
     */
    protected function createPermission($name, $slug, $path)
    {
        $permissionModel = config('admin.database.permissions_model');

        $permissionModel::create([
            'name'      => $name,
            'slug'      => $slug,
            'http_path' => '/'.trim($path, '/'),
        ]);
    }

    /**
     * Set routes for this extension.
     *
     * @param $callback
     */
    public function routes($callback)
    {
        $attributes = array_merge(
            [
                'prefix'     => config('admin.route.prefix'),
                'middleware' => config('admin.route.middleware'),
            ],
            $this->config('route', [])
        );

        Route::group($attributes, $callback);
    }

    /**
     * @return static
     */
    public static function make()
    {
        return new static();
    }
}
