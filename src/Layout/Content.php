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
     * @var bool
     */
    protected $usingPerfectScrollbar = true;

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
     * @param bool $value
     *
     * @return $this
     */
    public function disablePerfectScrollbar(bool $value = true)
    {
        $this->usingPerfectScrollbar = ! $value;

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
     * 页面滚动条优化.
     */
    protected function makePerfectScrollbar()
    {
        if (! $this->usingPerfectScrollbar) {
            return;
        }

        Admin::script(
            <<<'JS'
(function () {
    if ($(window).width() > 768) {
        var ps, wps;
        if ($('.full-page .wrapper').length) {
            wps = new PerfectScrollbar('.full-page .wrapper');
        }
        ps = new PerfectScrollbar('html');
        $(document).one('pjax:send',function () {
            ps && ps.destroy();
            ps = null; 
              
            wps && wps.destroy();
            wps = null; 
        });
    }
})()
JS
        );
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
            'theme' => '',
            'sidebar_collapsed' => false,
            'sidebar_dark' => false,
            'navbar_color' => '',
            'navbar_class' => 'sticky',
            'footer_type' => '',
            'body_class' => '',
        ];

        $data = array_merge(
            config('admin.layout') ?: [],
            $this->config
        );

        $allOptions = [
            'theme' => '',
            'footer_type' => '',
            'body_class' => '',
            'sidebar_dark' => '',
            'sidebar_collapsed' => [true, false],
            'navbar_color' => ['bg-primary', 'bg-info', 'bg-warning', 'bg-success', 'bg-danger', 'bg-dark'],
            'navbar_class' => ['floating' => 'floating-nav', 'sticky' => 'fixed-top', 'hidden' => 'd-none'],
        ];

        $maps = [
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

        return [
            'theme' => $data['theme'],
            'sidebar_collapsed' => $data['sidebar_collapsed'],
            'navbar_color' => $data['navbar_color'],
            'navbar_class' => $allOptions['navbar_class'][$data['navbar_class']],
            'sidebar_class' => $data['sidebar_collapsed'] ? 'sidebar-collapse' : '',
            'body_class' => $data['body_class'],
            'sidebar_dark' => $data['sidebar_dark'],
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

        $this->makePerfectScrollbar();

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
