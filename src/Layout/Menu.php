<?php

namespace Dcat\Admin\Layout;

use Dcat\Admin\Support\Helper;

class Menu
{
    /**
     * @var static
     */
    protected static $instance;

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
     * @var \Illuminate\Contracts\Routing\UrlGenerator
     */
    protected $url;

    public function __construct()
    {
        $this->url = url();
    }

    /**
     * Register menu,.
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
     * @param int $priority
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
     * @return string
     * @throws \Throwable
     */
    public function build(array $nodes)
    {
        $html = '';
        foreach (Helper::buildNestedArray($nodes) as $item) {
            $html .= view('admin::partials.menu', ['item' => &$item])->render();
        }

        return $html;
    }

    public function isActive(array $item, ?string $path = null)
    {
        if (empty($path)) {
            $path = request()->path();
        }

        if (empty($item['children'])) {
            if (empty($item['uri'])) {
                return false;
            }
            return trim($this->getFullUri($item['uri']), '/') == $path;
        }

        foreach($item['children'] as $v) {
            if ($path == trim($this->getFullUri($v['uri']), '/')) {
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

    public function getFullUri($uri)
    {
        if (!$uri) return $uri;

        return $this->url->isValidUrl($uri) ? $uri : admin_base_path($uri);
    }

    /**
     * @return static
     */
    public static function make()
    {
        return static::$instance ?: (static::$instance = new static);
    }
}
