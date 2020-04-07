<?php

namespace Dcat\Admin\Layout;

use Closure;
use Dcat\Admin\Admin;
use Dcat\Admin\Traits\HasBuilderEvents;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\ViewErrorBag;

class Content implements Renderable
{
    use HasBuilderEvents;

    /**
     * @var string
     */
    protected $view = 'admin::layouts.content';

    /**
     * @var array
     */
    protected $variables = [];

    /**
     * Content title.
     *
     * @var string
     */
    protected $title = '';

    /**
     * Content description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Page breadcrumb.
     *
     * @var array
     */
    protected $breadcrumb = [];

    /**
     * @var Row[]
     */
    protected $rows = [];

    /**
     * @var array
     */
    protected $config = [];

    /**
     * Content constructor.
     *
     * @param Closure|null $callback
     */
    public function __construct(\Closure $callback = null)
    {
        $this->callResolving();

        if ($callback instanceof Closure) {
            $callback($this);
        }
    }

    /**
     * Create a content instance.
     *
     * @param mixed ...$params
     *
     * @return $this
     */
    public static function make(...$params)
    {
        return new static(...$params);
    }

    /**
     * @param string $header
     *
     * @return $this
     */
    public function header($header = '')
    {
        return $this->title($header);
    }

    /**
     * Set title of content.
     *
     * @param string $title
     *
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set description of content.
     *
     * @param string $description
     *
     * @return $this
     */
    public function description($description = '')
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Build full page.
     *
     * @return $this
     */
    public function full()
    {
        $this->view = 'admin::layouts.full-content';

        return $this->withConfig('blank_page', true);
    }

    /**
     * Set breadcrumb of content.
     *
     * @example
     *     $this->breadcrumb('Menu', 'auth/menu', 'fa fa-align-justify');
     *     $this->breadcrumb([
     *         ['text' => 'Menu', 'url' => 'auth/menu', 'icon' => 'fa fa-align-justify']
     *     ]);
     *
     * @param array ...$breadcrumb
     *
     * @return $this
     */
    public function breadcrumb(...$breadcrumb)
    {
        $this->formatBreadcrumb($breadcrumb);

        $this->breadcrumb = array_merge($this->breadcrumb, $breadcrumb);

        return $this;
    }

    /**
     * @param array $breadcrumb
     *
     * @throws \Exception
     *
     * @return void
     */
    protected function formatBreadcrumb(array &$breadcrumb)
    {
        if (! $breadcrumb) {
            throw new \Exception('Breadcrumb format error!');
        }

        $notArray = false;
        foreach ($breadcrumb as &$item) {
            $isArray = is_array($item);
            if ($isArray && ! isset($item['text'])) {
                throw new \Exception('Breadcrumb format error!');
            }
            if (! $isArray && $item) {
                $notArray = true;
            }
        }
        if (! $breadcrumb) {
            throw new \Exception('Breadcrumb format error!');
        }
        if ($notArray) {
            $breadcrumb = [
                [
                    'text' => $breadcrumb[0] ?? null,
                    'url'  => $breadcrumb[1] ?? null,
                    'icon' => $breadcrumb[2] ?? null,
                ],
            ];
        }
    }

    /**
     * Alias of method row.
     *
     * @param mixed $content
     *
     * @return Content
     */
    public function body($content)
    {
        return $this->row($content);
    }

    /**
     * Add one row for content body.
     *
     * @param $content
     *
     * @return $this
     */
    public function row($content)
    {
        if ($content instanceof Closure) {
            $row = new Row();
            call_user_func($content, $row);
            $this->addRow($row);
        } else {
            $this->addRow(new Row($content));
        }

        return $this;
    }

    /**
     * @param $content
     *
     * @return $this
     */
    public function prepend($content)
    {
        if ($content instanceof Closure) {
            $row = new Row();
            call_user_func($content, $row);
            $this->prependRow($row);
        } else {
            $this->prependRow(new Row($content));
        }

        return $this;
    }

    protected function prependRow(Row $row)
    {
        array_unshift($this->rows, $row);
    }

    /**
     * Add Row.
     *
     * @param Row $row
     */
    protected function addRow(Row $row)
    {
        $this->rows[] = $row;
    }

    /**
     * Build html of content.
     *
     * @return string
     */
    public function build()
    {
        $html = '';

        foreach ($this->rows as $row) {
            $html .= $row->render();
        }

        return $html;
    }

    /**
     * Set success message for content.
     *
     * @param string $title
     * @param string $message
     *
     * @return $this
     */
    public function withSuccess($title = '', $message = '')
    {
        admin_success($title, $message);

        return $this;
    }

    /**
     * Set error message for content.
     *
     * @param string $title
     * @param string $message
     *
     * @return $this
     */
    public function withError($title = '', $message = '')
    {
        admin_error($title, $message);

        return $this;
    }

    /**
     * Set warning message for content.
     *
     * @param string $title
     * @param string $message
     *
     * @return $this
     */
    public function withWarning($title = '', $message = '')
    {
        admin_warning($title, $message);

        return $this;
    }

    /**
     * Set info message for content.
     *
     * @param string $title
     * @param string $message
     *
     * @return $this
     */
    public function withInfo($title = '', $message = '')
    {
        admin_info($title, $message);

        return $this;
    }

    /**
     * Set content view.
     *
     * @param null|string $view
     *
     * @return $this
     */
    public function view(?string $view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * @param string|array $key
     * @param mixed $value
     *
     * @return $this
     */
    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->variables = array_merge($this->variables, $key);
        } else {
            $this->variables[$key] = $value;
        }

        return $this;
    }

    /**
     * @param string|array $key
     * @param mixed $value
     *
     * @return $this
     */
    public function withConfig($key, $value = null)
    {
        if (is_array($key)) {
            $this->config = array_merge($this->config, $key);
        } else {
            $this->config[$key] = $value;
        }

        return $this;
    }

    /**
     * @return void
     */
    protected function shareDefaultErrors()
    {
        if (! session()->all()) {
            view()->share(['errors' => new ViewErrorBag()]);
        }
    }

    /**
     * @return array
     */
    protected function variables()
    {
        return array_merge([
            'header'          => $this->title,
            'description'     => $this->description,
            'breadcrumb'      => $this->breadcrumb,
            'configData'      => $this->applClasses(),
            'content'         => $this->build(),
            'pjaxContainerId' => Admin::$pjaxContainerId,
        ], $this->variables);
    }

    /**
     * @return array
     */
    protected function applClasses()
    {
        // default data array
        $defaultData = [
            'main_layout_type' => 'vertical',
            'theme' => 'light',
            'sidebar_collapsed' => false,
            'navbar_color' => '',
            'horizontal_menu_type' => 'floating',
            'vertical_menu_navbar_type' => 'floating',
            'footer_type' => 'static', //footer
            'body_class' => '',
            'content_layout' => 'default',
            'blank_page' => false,
            'direction' => env('MIX_CONTENT_DIRECTION', 'ltr'),
        ];

        $data = array_merge(
            config('admin.layout') ?: [],
            $this->config
        );

        // All options available in the template
        $allOptions = [
            'main_layout_type' => ['vertical', 'horizontal'],
            'theme' => ['light' => 'light', 'dark' => 'dark-layout', 'semi-dark' => 'semi-dark-layout'],
            'sidebar_collapsed' => [true, false],
            'navbar_color' => ['bg-primary', 'bg-info', 'bg-warning', 'bg-success', 'bg-danger', 'bg-dark'],
            'content_layout' => ['default', 'content-left-sidebar', 'content-right-sidebar', 'content-detached-left-sidebar', 'content-detached-right-sidebar'],
            'sidebar_position_class' => ['content-left-sidebar' => 'sidebar-left', 'content-right-sidebar' => 'sidebar-right', 'content-detached-left-sidebar' => 'sidebar-detached sidebar-left', 'content-detached-right-sidebar' => 'sidebar-detached sidebar-right', 'default' => 'default-sidebar-position'],
            'content_sidebar_class' => ['content-left-sidebar' => 'content-right', 'content-right-sidebar' => 'content-left', 'content-detached-left-sidebar' => 'content-detached content-right', 'content-detached-right-sidebar' => 'content-detached content-left', 'default' => 'default-sidebar'],
            'direction' => ['ltr', 'rtl'],
            'horizontal_menu_type' => ['floating' => 'navbar-floating', 'static' => 'navbar-static', 'sticky' => 'navbar-sticky'],
            'horizontal_menu_class' => ['static' => 'menu-static', 'sticky' => 'fixed-top', 'floating' => 'floating-nav'],
            'vertical_menu_navbar_type' => ['floating' => 'navbar-floating', 'static' => 'navbar-static', 'sticky' => 'navbar-sticky', 'hidden' => 'navbar-hidden'],
            'navbar_class' => ['floating' => 'floating-nav', 'static' => 'static-top', 'sticky' => 'fixed-top', 'hidden' => 'd-none'],
            'footer_type' => ['static' => 'footer-static', 'sticky' => 'fixed-footer', 'hidden' => 'footer-hidden'],
        ];

        $maps = [
            'content_layout' => 'sidebar_position_class',
            'horizontal_menu_type' => 'horizontal_menu_type',
            'vertical_menu_navbar_type' => 'vertical_menu_navbar_type',
            'footer_type' => 'footer_type',
        ];

        foreach ($allOptions as $key => $value) {
            if (! array_key_exists($key, $defaultData)) {
                continue;
            }

            if (! isset($data[$key])) {
                $data[$key] = $defaultData[$key];

                continue;
            }

            if (
                isset($maps[$key])
                && ! isset($allOptions[$maps[$key]][$data[$key]])
            ) {
                $data[$key] = $defaultData[$key];
            }
        }

        // layout classes
        return [
            'theme' => $data['theme'],
            'layout_theme' => $allOptions['theme'][$data['theme']] ?? $data['theme'],
            'sidebar_collapsed' => $data['sidebar_collapsed'],
            'vertical_menu_navbar_type' => $allOptions['vertical_menu_navbar_type'][$data['vertical_menu_navbar_type']],
            'navbar_class' => $allOptions['navbar_class'][$data['vertical_menu_navbar_type']],
            'navbar_color' => $data['navbar_color'],
            'horizontal_menu_type' => $allOptions['horizontal_menu_type'][$data['horizontal_menu_type']],
            'horizontal_menu_class' => $allOptions['horizontal_menu_class'][$data['horizontal_menu_type']],
            'footer_type' => $allOptions['footer_type'][$data['footer_type']],
            'sidebar_class' => $data['sidebar_collapsed'] ? 'menu-collapsed' : 'menu-expanded',
            'body_class' => $data['body_class'],
            'blank_page' => $data['blank_page'],
            'blank_page_class' => $data['blank_page'] ? 'blank-page' : '',
            'content_layout' => $data['content_layout'],
            'sidebar_position_class' => $allOptions['sidebar_position_class'][$data['content_layout']],
            'content_sidebar_class' => $allOptions['content_sidebar_class'][$data['content_layout']],
            'main_layout_type' => $data['main_layout_type'],
            'direction' => $data['direction'],
        ];
    }

    /**
     * Render this content.
     *
     * @return string
     */
    public function render()
    {
        $this->callComposing();
        $this->shareDefaultErrors();

        $variables = $this->variables();

        $this->callComposed();

        return view($this->view, $variables)->render();
    }

    /**
     * Register a composed event.
     *
     * @param callable $callback
     * @param bool     $once
     */
    public static function composed(callable $callback, bool $once = false)
    {
        static::addBuilderListeners('builder.composed', $callback, $once);
    }

    /**
     * Call the composed callbacks.
     *
     * @param array ...$params
     */
    protected function callComposed(...$params)
    {
        $this->fireBuilderEvent('builder.composed', ...$params);
    }
}
