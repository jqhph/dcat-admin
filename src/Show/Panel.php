<?php

namespace Dcat\Admin\Show;

use Dcat\Admin\Show;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Collection;

class Panel implements Renderable
{
    /**
     * The view to be rendered.
     *
     * @var string
     */
    protected $view = 'admin::show.panel';

    /**
     * The fields that this panel holds.
     *
     * @var Collection
     */
    protected $fields;

    /**
     * Variables in the view.
     *
     * @var array
     */
    protected $data;

    /**
     * Parent show instance.
     *
     * @var Show
     */
    protected $parent;

    /**
     * @var \Closure
     */
    protected $wrapper;

    /**
     * Panel constructor.
     */
    public function __construct(Show $show)
    {
        $this->parent = $show;

        $this->initData();
    }

    /**
     * Initialize view data.
     */
    protected function initData()
    {
        $this->data = [
            'fields' => new Collection(),
            'tools'  => new Tools($this),
            'style'  => 'default',
            'title'  => trans('admin.detail'),
        ];
    }

    /**
     * Set parent container.
     *
     * @param Show $show
     *
     * @return $this
     */
    public function setParent(Show $show)
    {
        $this->parent = $show;

        return $this;
    }

    /**
     * Get parent container.
     *
     * @return Show
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set style for this panel.
     *
     * @param string $style
     *
     * @return $this
     */
    public function style($style = 'info')
    {
        $this->data['style'] = $style;

        return $this;
    }

    /**
     * Set title for this panel.
     *
     * @param string $title
     *
     * @return $this
     */
    public function title($title)
    {
        $this->data['title'] = $title;

        return $this;
    }

    /**
     * Set view for this panel to render.
     *
     * @param string $view
     *
     * @return $this
     */
    public function view($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Add variables to show view.
     *
     * @param array $variables
     *
     * @return $this
     */
    public function with(array $variables = [])
    {
        $this->data = array_merge($this->data, $variables);

        return $this;
    }

    /**
     * @return $this
     */
    public function wrap(\Closure $wrapper)
    {
        $this->wrapper = $wrapper;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasWrapper()
    {
        return $this->wrapper ? true : false;
    }

    /**
     * Build panel tools.
     *
     * @param $callable
     *
     * @return Tools|null
     */
    public function tools($callable = null)
    {
        if ($callable === null) {
            return $this->data['tools'];
        }

        call_user_func($callable, $this->data['tools']);
    }

    /**
     * Fill fields to panel.
     *
     * @param []Field $fields
     *
     * @return $this
     */
    public function fill($fields)
    {
        $this->data['fields'] = $fields;

        return $this;
    }

    /**
     * Render this panel.
     *
     * @return string
     */
    public function render()
    {
        return $this->doWrap();
    }

    /**
     * @return string
     */
    protected function doWrap()
    {
        $view = view($this->view, $this->data);

        if (! $wrapper = $this->wrapper) {
            return "<div class='card da-box'>{$view->render()}</div>";
        }

        return $wrapper($view);
    }
}
