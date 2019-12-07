<?php

namespace Dcat\Admin\Layout;

use Dcat\Admin\Admin;
use Dcat\Admin\Support\Helper;

class Menu
{
    /**
     * @var array
     */
    protected static $helperNodes = [
        [
            'id'        => 1,
            'title'     => 'Helpers',
            'icon'      => 'fa-gears',
            'uri'       => '',
            'parent_id' => 0,
        ],
        [
            'id'        => 2,
            'title'     => 'Extensions',
            'icon'      => 'fa-plug',
            'uri'       => 'helpers/extensions',
            'parent_id' => 1,
        ],
        [
            'id'        => 3,
            'title'     => 'Scaffold',
            'icon'      => 'fa-keyboard-o',
            'uri'       => 'helpers/scaffold',
            'parent_id' => 1,
        ],
        [
            'id'        => 4,
            'title'     => 'Routes',
            'icon'      => 'fa-internet-explorer',
            'uri'       => 'helpers/routes',
            'parent_id' => 1,
        ],
        [
            'id'        => 5,
            'title'     => 'Icons',
            'icon'      => 'fa-fonticons',
            'uri'       => 'helpers/icons',
            'parent_id' => 1,
        ],
    ];

    /**
     * Register menu.
     */
    public function register()
    {
        if (!admin_has_default_section(\AdminSection::LEFT_SIDEBAR_MENU)) {
            admin_inject_default_section(\AdminSection::LEFT_SIDEBAR_MENU, function () {
                $menuModel = config('admin.database.menu_model');

                return $this->build((new $menuModel())->allNodes());
            });
        }

        if (config('app.debug')) {
            // Register the menu of helpers.
            $this->add(static::$helperNodes, 20);
        }
    }

    /**
     * @param array $nodes
     * @param int   $priority
     */
    public function add(array $nodes = [], int $priority = 10)
    {
        admin_inject_section(\AdminSection::LEFT_SIDEBAR_MENU_BOTTOM, function () use (&$nodes) {
            return $this->build($nodes);
        }, true, $priority);
    }

    /**
     * Build html.
     *
     * @param array $nodes
     *
     * @throws \Throwable
     *
     * @return string
     */
    public function build(array $nodes)
    {
        $html = '';
        foreach (Helper::buildNestedArray($nodes) as $item) {
            $html .= $this->renderMenu($item);
        }

        return $html;
    }

    /**
     * @param array $item
     *
     * @return array|string
     */
    protected function renderMenu(array $item)
    {
        return view('admin::partials.menu', ['item' => &$item])->render();
    }

    /**
     * @param array       $item
     * @param null|string $path
     *
     * @return bool
     */
    public function isActive(array $item, ?string $path = null)
    {
        if (empty($path)) {
            $path = request()->path();
        }

        if (empty($item['children'])) {
            if (empty($item['uri'])) {
                return false;
            }

            return trim($this->getPath($item['uri']), '/') == $path;
        }

        foreach ($item['children'] as $v) {
            if ($path == trim($this->getPath($v['uri']), '/')) {
                return true;
            }
            if (!empty($v['children'])) {
                if (static::isActive($v, $path)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param array $menuItem
     *
     * @return bool
     */
    public function show(array $menuItem)
    {
        $permissionIds = $menuItem['permission_id'] ?? null;
        $roles = array_column($menuItem['roles'] ?? [], 'slug');
        $permissions = array_column($menuItem['permissions'] ?? [], 'slug');

        if (!$permissionIds && !$roles && !$permissions) {
            return true;
        }

        $user = Admin::user();

        if ($user->visible($roles)) {
            return true;
        }

        foreach (array_merge(Helper::array($permissionIds), $permissions) as $permission) {
            if ($user->can($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $uri
     *
     * @return string
     */
    public function getPath($uri)
    {
        if (!$uri) {
            return $uri;
        }

        return url()->isValidUrl($uri) ? $uri : admin_base_path($uri);
    }

    /**
     * @param $uri
     *
     * @return string
     */
    public function getUrl($uri)
    {
        if (!$uri) {
            return $uri;
        }

        return admin_url($uri);
    }
}
